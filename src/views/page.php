<hr/>
<? 

echo $data["answer"];

?>
<hr/>

<h2>Добавить в базу</h2>
        <form method='post'>
            <textarea name='add' style='width:600px; height:100px;' value='' placeholder='Введите json' ></textarea>
            <p><input type='submit' value='Добавить'/></p>
        </form>
        <h3>Пример данных для отправки</h3>
        <pre>
        
{
    "event": "event1",
    "priority": 1000,
    "conditions": {
        "param1": 1,
        "param2": 2 
    }
}
        
        </pre>



        <hr/>
        <h2>Искать по базе</h2>
        <form method='post'>
            <textarea name='search' style='width:600px; height:100px;' value='' placeholder='Введите json' ></textarea>
            <p><input type='submit' value='Искать'/></p>
        </form>
        
        <h3>Пример данных для отправки</h3>
        <pre>
        
{
    "params": {
        "param1": 1,
        "param2": 2 
    }
}
        
        </pre>


        <hr/>
        <h2>Очистить базу</h2>
        <form method='post'>
            <p><input type='submit' name='clear' value='Очистить базу'/></p>
        </form>