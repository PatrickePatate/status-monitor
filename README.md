![Status Monitor](https://i.ibb.co/Rb8dRct/logo.png#gh-light-mode-only)
![Status Monitor](https://i.ibb.co/2gPvMCm/logo-white.png#gh-dark-mode-only)

## About Status Monitor

Status Monitor is a simple web app built to be deployed easily with docker and easy to configure. Status Monitor is another status page app that includes internal automatic monitoring of your services and a public api to integrate with your other tools.

Status Monitor has Metrics, HTTP and DNS checks, a public status page, status badges...

## Installation

Just clone this project and run :
- ```vendor/bin/sail build --no-cache```
- ```vendor/bin/sail up -d```
- ```vendor/bin/sail npm install```
- ```vendor/bin/sail npm run build```
- ```php artisan make:filament-user```
- ```php artisan filament:assets```
- ```php artisan optimize```
- ```php artisan filament:cache-components```

Status Page is public at yourdomain.com/, et you can access admin dashboard at yourdomain.com/dashboard.

## Contribute to Status Monitor

I built Status Monitor because I was unable to find a simple status page that included metrics and monitoring without being a hell to setup and start using.
But I'm the unique developper of this app so I'll be accepting contributions happily.

## How does it work ?

Status Monitor uses Laravel has a backend to execute checks and serve the public status page. 
You start by creating *services* that have a name and basic infos. Then, you can setup metrics for your service and checks that will monitor it and input data into your metrics.

![How does Status Monitor works](https://i.ibb.co/fkK8GJj/fonctionnement-sm.png)

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to me via [hi@lucienpuget.fr](mailto:hi@lucienpuget.fr) or open a PR. I'll try to do my best.

## License

Status Monitor is an open-sourced software licensed under the [GPL license](https://opensource.org/licenses/GPL-3-0).
