async function sendRequest() {

    // Delete previous requests data
    var output_div = document.getElementById("output");
    output_div.style.display = "none";
    var result_div = document.getElementById("result");
    result_div.innerHTML = "";

    // Get the URL
    var url_req = document.getElementById('url-req');
    var url = url_req.value;

    // Get the body
    var body_req = document.getElementById("body-req");
    var body_value = body_req.value;

    // Check if the URL parameter is filled
    if (url == "" || url == null){
        url_req.style.borderColor = "red";
        document.getElementById('error-req').innerHTML = "URL can't be empty";

        // Reset the errors related with filling the body textarea
        body_req.style.borderColor = "#a9a9a9";
    } else {

        // Reset the errors related with empty URL
        url_req.style.borderColor = "#a9a9a9";
        document.getElementById('error-req').innerHTML = "";
        
        // Get the method
        var op_req = document.getElementById("op-req");
        var operation = op_req.options[op_req.selectedIndex].text;

        if (operation == "GET" || operation == "HEAD") {
            var settings = {
                method: operation
            }

            // Show error if GET or HEAD methods have body
            if (body_value != "" && body_value != null){
                document.getElementById('error-req').innerHTML = "Body data omitted because you selected method " + operation;
                body_req.style.borderColor = "red";
            } else {

                // Reset the errors related with filling the body textarea
                body_req.style.borderColor = "#a9a9a9";
            }
        } else {
            var settings = {
                method: operation,
                body: body_value
            }

            // Reset the errors related with filling the body textarea
            body_req.style.borderColor = "#a9a9a9";
        }

        // Show loading icon
        document.getElementById("overlap-loading").style.display = "block";

        // Async request
        try {
            var result = await fetch(url, settings)
            .then((response) => {
                return response.json();
            }).then((data) => {
                return data;
            }); 

            // Show the result
            output_div.style.display = "block";
            result_div.innerHTML = JSON.stringify(result, null, 4);
        } catch (error) {
            alert("Something went wrong");
        }

        // Hide loading icon
        document.getElementById("overlap-loading").style.display = "none";
    }
}