 <script>

    let jsonEditor = document.querySelector('#jsonEditor')
    let aceJSONEditor = ace.edit("jsonEditor");

    aceJSONEditor.setTheme('ace/theme/monokai')
    aceJSONEditor.session.setMode("ace/mode/json");

    aceJSONEditor.setReadOnly(true)

    function updateJSON(e) {
        let data = document.getElementsByClassName(e.value)[0].innerHTML
        let jsonData = JSON.stringify(JSON.parse(data), null, '\t')
        aceJSONEditor.session.setValue(jsonData)
    }

 </script>

