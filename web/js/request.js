async function sendRequest() {

    // Get the URL
    var url_req = document.getElementById('url-req');
    var url = url_req.value;

    // Check if the URL parameter is filled
    if (url == "" || url == null){
        url_req.style.borderColor = "red";
        document.getElementById('error-req').innerHTML = "URL can't be empty";
    } else {

        // Reset the errors
        url_req.style.borderColor = "#a9a9a9";
        document.getElementById('error-req').innerHTML = "";
        
        // Get the method
        var op_req = document.getElementById("op-req");
        var operation = op_req.options[op_req.selectedIndex].text;

        // GET and HEAD can't have body
        if (operation == "GET" || operation == "HEAD") {
            var settings = {
                method: operation
            }
        } else {
            var body_req = document.getElementById("body-req");
            var body_value = body_req.value;
    
            var settings = {
                method: operation,
                body: body_value
            }
        }

        // Async request
        var result = await fetch(url, settings)
        .then((response) => {
            return response.json();
        }).then((data) => {
            return data;
        }); 
        
        console.log(result);
    }
}