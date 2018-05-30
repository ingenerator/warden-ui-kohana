### Unreleased

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
