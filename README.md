Helper.NotFound
===============

TYPO3 Neos package that loads a Neos page for displaying a 404 error. Also working on multilanguage pages.

Works with TYPO3 Neos 1.0-2.0+

Installation
------------
```composer require "helper/notfound" "1.0.*"```

Create a page with the URI segment "404" in the root of your site.

Alternatively set the following configuration in ``Settings.yaml``:

```yaml
  TYPO3:
    Flow:
      error:
        exceptionHandler:
          renderingGroups:
            notFoundExceptions:
              options:
                variables:
                  # Path to 404 error page relative to site root
                  path: '404'
```
