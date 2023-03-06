# Sentry.io Module for SailCMS

This is the official Sentry.io package for SailCMS. Sentry.io is a great application performance and issue tracking
service.

## Installing

```bash
php sail install:official leeroy/sail-sentryio
```

This will install the package using composer and then update your composer file to autoload the package.

If you wish to install it manually, you and perform the following

```bash
composer require leeroy/sail-sentryio
```

After that, you can add `Leeroy\\Sentry` to the modules section of the sailcms property of your composer.json file. It should look something like this:

```json
"sailcms": {
  "containers": ["Spec"],
  "modules": [
    "Leeroy\\Sentry"
  ],
  "search": {}
}
```

## Configuration

When installed, you need to add the following to your `.env` file.

```
SENTRY_DSN="https://xxxxxxxxxxxxx.ingest.sentry.io/xxxxxxxxxxxxxx"
SENTRY_ORG="yourProjectOrOrganization"
```

## Using

Activating the package is automatic. The only this you can use afterwards is custom exception handling.

Here are the 3 available methods to that.

### capture

This captures the exception and nothing more.

```php
try {
    //...
} catch (Exception $e) {
    Leeroy\Sentry::capture($e);
}
```

### captureWithContext

This is a more advance use than capture. With this, you can capture the exception, add context and tags for it and 
a custom message to display in sentry's UI.

```php
try {
    //...
} catch (Exception $e) {
    Leeroy\Sentry::capture(
        $e, 
        'yourContextName', 
        ['your' => 'context data'], // don't add too much data
        ['tag1', 'tag2'], 
        'custom message!'
    );
}
```

### captureLastError

This captures the last error and sends it to Sentry.