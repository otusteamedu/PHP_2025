--  Генерация номера телефона
CREATE OR REPLACE FUNCTION generate_random_phone_number() RETURNS TEXT AS
$$ DECLARE
    area_code   TEXT := '7'; -- Коды стран можно изменить по необходимости
    first_part  TEXT;
    second_part TEXT;
    third_part  TEXT;
BEGIN
    first_part := LPAD(TRUNC(RANDOM() * 1000)::TEXT, 3, '0'); -- Генерация первых трех цифр
    second_part := LPAD(TRUNC(RANDOM() * 10000)::TEXT, 3, '0'); -- Генерация следующих трех цифр
    third_part := LPAD(TRUNC(RANDOM() * 100)::TEXT, 4, '0'); -- Генерация четырех цифр
    RETURN '+' || area_code || ' (' || first_part || ') ' || second_part || '-' || third_part;
END;
$$ LANGUAGE plpgsql;


-- Генерация строки по количеству
CREATE OR REPLACE FUNCTION generate_random_string_by_int(length INTEGER) RETURNS TEXT AS
$$ DECLARE
    chars  TEXT[]  := '{0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z}';
    result TEXT    := '';
    i      INTEGER := 0;
BEGIN
    IF length < 0 THEN
        RAISE EXCEPTION 'Given length cannot be less than 0'; -- Заданная длина не может быть меньше
    END IF;

    FOR i IN 1..length
        LOOP
            result := result || chars[1 + random() * (array_length(chars, 1) - 1)];
        END LOOP;
    RETURN result;
END;
$$ LANGUAGE plpgsql;


-- Генерация чисел от min до max
CREATE OR REPLACE FUNCTION generate_random_int_by_min_and_max(min_value INTEGER, max_value INTEGER) RETURNS INTEGER AS
$$ BEGIN
    RETURN random() * (max_value - min_value) + min_value;
END
$$ LANGUAGE plpgsql;


-- Генерация времени
CREATE OR REPLACE FUNCTION generate_random_timestamp() RETURNS TIMESTAMP AS
$$ BEGIN
    RETURN CURRENT_DATE - 365 + (random() * 365 * 2)::INT
        + date_trunc('hour',('10:00'::TIME + (random() * ('23:00'::TIME - '10:00'::TIME)))::TIME);
END;
$$ LANGUAGE plpgsql;
