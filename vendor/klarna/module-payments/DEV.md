# Class annotations

In all classes you will find @api and @internal annotations. The meaning of these are the following:
- @api: Public api - see: https://docs.phpdoc.org/guide/references/phpdoc/tags/api.html
- @internal: Internal api - see: https://docs.phpdoc.org/guide/references/phpdoc/tags/internal.html

In the end this means that anyone can overwrite and extend classes or methods with different techniques (Plugin, ...)
when there is a @api annotation since there will be no breaking change in the current major version. When there is the
@internal annotation then it is recommended to not extend or overwrite this class because in this class can be always
a breaking change for example the class is moved to a different folder or removed.
