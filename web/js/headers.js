function newElement() {
    var inputName = document.getElementById("name").value.trim();
    var inputValue = document.getElementById("value").value.trim();
    if (inputName == "" || inputName == null || inputValue == "" || inputValue == null) {
        alert("You must fill both inputs");
    } else {

        // Append the text
        var li = document.createElement("li");
        var p = document.createElement("p");
        var name = document.createElement("span");
        name.textContent = inputName
        var colon = document.createElement("span");
        colon.textContent = ": ";
        var value = document.createElement("span");
        value.textContent = inputValue
        p.appendChild(name);
        p.appendChild(colon);
        p.appendChild(value);
        li.appendChild(p);
        document.getElementById("headers-list").appendChild(li);

        // Append the X button
        var span = document.createElement("span");
        var x = document.createTextNode("\u00D7");
        span.className = "close2";
        span.appendChild(x);
        li.appendChild(span);
    
        // Event listener for the X button
        span.onclick = function() {
            var li_parent = this.parentElement;
            li_parent.remove();
        }
    }

    // Reset the inputs
    document.getElementById("name").value = "";
    document.getElementById("value").value = "";
}