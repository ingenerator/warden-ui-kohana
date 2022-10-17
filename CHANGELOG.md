### Unreleased

* Support PHP 8.1

### v1.3.0 (2021-04-21)

* Support php8.0

### v1.2.0 (2020-11-02)

* Support php7.4

### v1.1.0-beta1 (2020-05-14)

* Add LogMetadataProvider to include logged-in user email in app and request logs
* Now requires kohana-extras:2.x for enhanced logging support

### v1.0.0 (2018-04-04)

* Ensure support for php7.2
* Drop support for php5

### v0.4.1 (2018-12-06)

* Support kohana-extras 0.4 release (merge-up of 0.3.4 release)

### v0.4.0 (2018-10-04)

* [Feature]  Support global and per-account rate limiting of login attempts. Customise the
  warden.login.global and warden.login.user bucket_types in config/warden.php to set 
  site-specific limits.
* [Feature]  Pre-validate registration and password reset links and show the user an error
  immediately rather than on save if the link they're using has expired.
* [Feature]  Handle cases where a user's email fails MX validation at time of login and 
  a password reset / activation therefore cannot be sent.
* [Feature]  Provide `LastLoginTrackingUser` interface to identify entities that have a 
  property(/ies) that should be updated every time the user logs in - for example last login
  time. Apply this to your entity to have KohanaUserSession update and persist the user on
  every login.
* [Feature]  Handle `inactive account` response on login and add controller etc to handle
  users clicking through on activation links.
* [Feature]  Add controller etc for authenticated user to change their own password.
* [BREAKING] Add optional controller endpoints for triggering and completing change to a 
  verified new user email address. If you don't want to expose this, set route_controller to 
  FALSE for the `change-email` and `complete-change-email` actions in config/warden.php 
* [Feature]  Handle and log rate-limited email verification attempts for login and registration
* [BREAKING] Update dependency definitions for rate-limiting in warden-core 
* [BREAKING] Update user repository and notification mailer interfaces in line 
  with warden-core 0.3. 

### v0.3.4 (2018-12-06)

* Support kohana-extras 0.4 release

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
