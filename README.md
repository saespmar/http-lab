<p align="center">
  <img src="https://raw.githubusercontent.com/saespmar/http-lab/master/web/images/Logo.png" alt="logo">
</p>

# HTTP Lab
This web application allows developers to make custom HTTP requests for development and testing purposes. It includes a movie REST service to test basic functionality.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine.

### Prerequisites

You don't need to install anything in order to make the web application run. Opening the [main page](/web/index.html) with any web browser is enough. 

However, this project can be portable and can have a test REST service included. In this case, you need to install **Docker**.

### Deploying with Docker

Execute the following commands in the project file:

```
docker build -t php7mongodb .
docker-compose up --build
```

Now you can access the web application and the REST service through the IP Docker provides.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details
