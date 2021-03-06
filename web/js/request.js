async function sendRequest() {

    // Delete previous requests data
    var output_div = document.getElementById("output");
    output_div.style.display = "none";
    var metadata_div = document.getElementById("metadata");
    metadata_div.innerHTML = "";
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

        var settings = {
            method: operation
        }

        // Show error if GET or HEAD methods have body
        if (operation == "GET" || operation == "HEAD") {
            if (body_value != "" && body_value != null){
                document.getElementById('error-req').innerHTML = "Body data omitted because you selected method " + operation;
                body_req.style.borderColor = "red";
            } else {

                // Reset the errors related with filling the body textarea
                body_req.style.borderColor = "#a9a9a9";
            }
        } else {
            settings.body = body_value;

            // Reset the errors related with filling the body textarea
            body_req.style.borderColor = "#a9a9a9";
        }

        // Get the advanced options of the modal
        var modal_mode = document.getElementById("modal-mode");
        settings.mode = modal_mode.options[modal_mode.selectedIndex].value;

        var modal_credentials = document.getElementById("modal-credentials");
        settings.credentials = modal_credentials.options[modal_credentials.selectedIndex].value;

        var modal_cache = document.getElementById("modal-cache");
        settings.cache = modal_cache.options[modal_cache.selectedIndex].value;

        var modal_redirect = document.getElementById("modal-redirect");
        settings.redirect = modal_redirect.options[modal_redirect.selectedIndex].value;

        // Add the headers, if necessary
        var ul = document.getElementById("headers-list");
        var items = ul.getElementsByTagName("li");
        if (items.length > 0){
            var headers = new Headers();
            for (var i = 0; i < items.length; i++) {
                var spanList = items[i].getElementsByTagName("p")[0].getElementsByTagName("span"); 
                headers.append(spanList[0].textContent, spanList[2].textContent);
            }
            settings.headers = headers;
        }

        // Show loading icon
        document.getElementById("overlap-loading").style.display = "block";

        // Async request
        try {
            var result = await fetch(url, settings)
            .then((response) => {

                // Show metadata
                var p = document.createElement("p");
                p.textContent = 'Status: ' + response.status + ' ' + response.statusText;
                metadata_div.appendChild(p);

                p = document.createElement("p");
                p.textContent = 'Response Type: ' + response.type;
                metadata_div.appendChild(p);

                p = document.createElement("p");
                p.textContent = 'Request URL: ' + response.url;
                metadata_div.appendChild(p);
                
                for (var pair of response.headers.entries()) {
                    p = document.createElement("p");
                    p.textContent = pair[0]+ ': '+ pair[1];
                    metadata_div.appendChild(p);
                }

                // Check if it's a JSON response
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") !== -1) {
                    return response.json().then(data => {

                        // Show the result
                        output_div.style.display = "block";
                        result_div.textContent = JSON.stringify(data, null, 4);
                    });
                } else {
                    return response.text().then(text => {
                        output_div.style.display = "block";
                        result_div.textContent = text;
                    });
                }
            });
        } catch (error) {
            alert("Something went wrong");
        }

        // Hide loading icon
        document.getElementById("overlap-loading").style.display = "none";
    }
}