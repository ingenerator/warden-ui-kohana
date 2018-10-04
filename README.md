Provides a kohana-backed implementation of the [ingenerator/warden](https://github.com/ingenerator/warden) user
auth / management library.

**Warden is under heavy development and not recommended for production use outwith inGenerator.**

[![Build Status](https://travis-ci.org/ingenerator/warden-ui-kohana.svg?branch=0.4.x)](https://travis-ci.org/ingenerator/warden-ui-kohana)


# Installing warden-ui-kohana

This isn't in packagist yet : you'll need to add our package repository to your composer.json:

```json
{
  "repositories": [
    {"type": "composer", "url": "https://php-packages.ingenerator.com"}
  ]
}
```

`$> composer require ingenerator/warden-ui-kohana`

Then add it as a module in your bootstrap.

# Contributing

Contributions are welcome but please contact us before you start work on anything to check your
plans line up with our thinking and future roadmap. 

# Contributors

This package has been sponsored by [inGenerator Ltd](http://www.ingenerator.com)

* Andrew Coulton [acoulton](https://github.com/acoulton) - Lead developer

# Licence

Licensed under the [BSD-3-Clause Licence](LICENSE)
