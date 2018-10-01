### Unreleased

* [BREAKING]  Add optional controller endpoints for triggering and completing change to a 
  verified new user email address. If you don't want to expose this, set route_controller to 
  FALSE for the `change-email` and `complete-change-email` actions in config/warden.php 
* [FEATURE]  Handle and log rate-limited email verification attempts for login and registration
* [BREAKING] Update dependency definitions for rate-limiting in warden-core 
* [BREAKING] Update user repository and notification mailer interfaces in line 
  with warden-core 0.3. 

### v0.3.3 (2018-09-26)

* Log all failed logins
* Extract all flash message texts to message file rather than hardcoding

### v0.3.2 (2018-09-04)

* Add support for PHP ^7.2

### v0.3.1 (2018-05-30)

* Login form provides submission URL in action param to enable the form to be 
  embedded on different pages

### v0.3.0 (2018-03-14)

* Implement a flexible config-based UrlProvider that is both able to generate URLs for
  the most common cases Warden needs them, and to define the application routing to map 
  those back to controllers.
* Provide WardenKohanaDependencyFactory::definitions and ::controllerDefinitions
* Now officially requires ingenerator/tokenista as the default token-generating service
* Split all actions to individual controllers - these will now need to be routed as HTTPMethodRoutes for each one,
  and reverse routing may well need to change
* Require the inGenerator fork of Kohana

### v0.2.1 (2018-03-12)

* Regenerate session ID on login / logout 

### v0.2.0 (2018-02-20)

* Bump kohana-extras dependency to 0.2

### v0.1.0 (2018-02-13)

* First version, extracted from host project
