<p align="center"><a href="#coming" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Status Monitor logo"></a></p>

## About Status Monitor

Status Monitor is a simple web app built to be deployed easily with docker and easy to configure. Status Monitor is another status page app that includes internal automatic monitoring of your services and a public api to integrate with your other tools.

Status Monitor has Metrics, HTTP and DNS checks, a public status page, status badges...
![How does Status Monitor works](https://github.com/PatrickePatate/status_monitor/blob/main/storage/app/public/fonctionnement_sm.png?raw=true)

## Contribute to Status Monitor

I built Status Monitor because I was unable to find a simple status page that included metrics and monitoring without being a hell to setup and start using.
But I'm the unique developper of this app so I'll be accepting contributions happily.

## How does it work ?

Status Monitor uses Laravel has a backend to execute checks and serve the public status page. 
You start by creating *services* that have a name and basic infos. Then, you can setup metrics for your service and checks that will monitor it and input data into your metrics.

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [hi@lucienpuget.fr](mailto:hi@lucienpuget.fr). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [GPL license](https://opensource.org/licenses/GPL-3-0).
