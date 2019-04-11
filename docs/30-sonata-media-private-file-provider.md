Sonata Media Private File Provider
==================================

This file provider extends the file provider of [Sonata Media package][1] and allows to store
media files on a non public place.

The main purpose of this provider is to store media that are private and should not have 
a public url.

Configuration
-------------

```yaml
sonata_helpers:
    sonata_media_private_file_provider:
        url_prefix: '/admin/private' #default
        storage_path: '%kernel.project_dir%/data/media' #default
        allowed_extensions: [] #default
        allowed_mime_types: [] #default
```

The `allowed_extensions` & `allowed_mime_types` if they are empty or not configured will be populated 
with [default values from SonataMedia file provider][2].

Todo (comming soon)
-------------------
- controller to access file and check download strategy on related context
- validate that storage_path does not start by a public path like web/upload or public (SF 3 & 4)

[1]: https://github.com/sonata-project/SonataMediaBundle
[2]: https://github.com/sonata-project/SonataMediaBundle/blob/master/src/DependencyInjection/Configuration.php#L323
