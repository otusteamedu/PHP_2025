#!/usr/bin/env tarantool

-- настройка Tarantool
box.cfg {
    listen = 3301,
    checkpoint_interval = 60,
    checkpoint_count = 6,
    wal_mode = 'write',
    log_level = 5
}

-- ждем пока box будет готов
box.once('init', function()
    -- получаем переменные окружения
    local username = os.getenv('TARANTOOL_USER_NAME') or 'admin'
    local password = os.getenv('TARANTOOL_USER_PASSWORD') or 'password'
    
    -- создаем пользователя для подключения
    box.schema.user.create(username, {password = password, if_not_exists = true})
    box.schema.user.grant(username, 'read,write,execute', 'universe', nil, {if_not_exists = true})
    
    -- создаем пространство для событий
    local events_space = box.schema.space.create('events', {if_not_exists = true})
    
    -- создаем индексы
    events_space:create_index('primary', {
        parts = {1, 'string'}, -- id
        if_not_exists = true
    })
    
    events_space:create_index('priority', {
        parts = {2, 'number'}, -- priority
        if_not_exists = true,
        unique = false
    })
    
    print('Tarantool initialized successfully!')
    print('Events space created with indexes')
    print('User created: ' .. username)
end)

-- функция для добавления события
function add_event(id, priority, conditions, event_data)
    local created_at = os.time()
    return box.space.events:insert{id, priority, conditions, event_data, created_at}
end

-- функция для получения всех событий
function get_all_events()
    local events = {}
    for _, tuple in box.space.events.index.priority:pairs(nil, {iterator = 'REQ'}) do
        table.insert(events, {
            id = tuple[1],
            priority = tuple[2],
            conditions = tuple[3],
            event = tuple[4],
            created_at = tuple[5]
        })
    end
    return events
end

-- функция для очистки всех событий
function clear_all_events()
    box.space.events:truncate()
    return true
end

-- функция для поиска событий по условиям
function find_matching_events(params)
    local matching_events = {}
    
    for _, tuple in box.space.events.index.priority:pairs(nil, {iterator = 'REQ'}) do
        local conditions = tuple[3]
        local matches = true
        
        -- проверяем соответствие всех условий
        for key, value in pairs(params) do
            if conditions[key] == nil or tostring(conditions[key]) ~= tostring(value) then
                matches = false
                break
            end
        end
        
        if matches then
            table.insert(matching_events, {
                id = tuple[1],
                priority = tuple[2],
                conditions = tuple[3],
                event = tuple[4],
                created_at = tuple[5]
            })
        end
    end
    
    return matching_events
end

-- функция для получения статистики
function get_event_stats()
    local count = box.space.events:count()
    local total_priority = 0
    local max_priority = 0
    local min_priority = math.huge
    
    for _, tuple in box.space.events:pairs() do
        local priority = tuple[2]
        total_priority = total_priority + priority
        if priority > max_priority then max_priority = priority end
        if priority < min_priority then min_priority = priority end
    end
    
    local avg_priority = count > 0 and total_priority / count or 0
    if min_priority == math.huge then min_priority = 0 end
    
    return {
        total_events = count,
        avg_priority = avg_priority,
        max_priority = max_priority,
        min_priority = min_priority
    }
end

print('Tarantool is ready to accept connections on port 3301') 