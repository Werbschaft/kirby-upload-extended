# Kirby Upload Extended

More options when uploading files like name changes, resizing via Kirby or compression and optional resizing via TinyPNG. Thanks at this point also to @medienbaecker for Parts of the code for pure resize with Kirby. It is the best alternative when only pure resizing is needed during upload. https://github.com/medienbaecker/kirby-autoresize

**The individual components of the plugin:**

- Name change and replacement
- Kirby size change
- Optimization of images by TinyPNG
- Resizing with TinyPNG
- Upload control for other file types

**Note:** this is my first plugin for Kirby. Small bugs here and there are possible.

## Installation

Download and copy this repository to `/site/plugins/upload-extended`. Sorry, currently no Composer

## Configuration and options

There are a few options for the plugin. Every single function can be enabled or disabled. Below are the default settings for the `config.php`.

```php
return [
  'werbschaft.uploadExtended.rename' => true,
  'werbschaft.uploadExtended.excludeCharacters' => ['_','__','___','--','---'],
  'werbschaft.uploadExtended.kirbyResize' => true,
  'werbschaft.uploadExtended.maxWidth' => 2000,
  'werbschaft.uploadExtended.maxHeight' => 2000,
  'werbschaft.uploadExtended.quality' => 100,
  'werbschaft.uploadExtended.debug' => false,
  'werbschaft.uploadExtended.tinyPng' => true,
  'werbschaft.uploadExtended.tinyPngKey' => 'insert-here',
  'werbschaft.uploadExtended.tinyPngResize' => false,
  'werbschaft.uploadExtended.tinyPngResizeMethod' => 'thumb',
  'werbschaft.uploadExtended.excludeTemplates' => [],
  'werbschaft.uploadExtended.excludePages' => [],
  'werbschaft.uploadExtended.uploadLimit' => true,
  'werbschaft.uploadExtended.uploadLimitMegabyte' => 5, 
];
```

## Options in detail

Option | Type | Function
------------ | ------------- | -------------
rename | Bool | Should the files be renamed during upload
excludeCharacters | Array | Which strings should be replaced with a -
kirbyResize | Bool | Should images be checked for size when uploaded in Kirby and scaled down if necessary
maxWidth | Int | Maximum width of an image in pixels
maxHeight | Int | Maximum height of an image in pixels
quality | Int | Quality of the uploaded image
debug | Bool | If active, various details are output with each upload what was changed by the plugin
tinyPng | Bool | Should the images be optimized by the TinyPNG service during upload? Requires a valid API key
tinyPngKey | String | The valid API key of your account
tinyPngResize | Bool | Images can also be resized by TinyPNG. Attention: per resize this uses one credit extra
tinyPngResizeMethod | String | If the TinyPNG Resize is used, which method should be applied: https://tinypng.com/developers/reference/php#resizing-images
excludeTemplates | Array | Array of page templates to exclude
excludePages | Array | Array of pages to exclude
uploadLimit | Bool | Should other files, except images, be checked for size when uploaded
uploadLimitMegabyte | Int | The Maximum Upload Limit. Files that are larger will be deleted immediately

## Known problems and future

- [ ] Replace files in combination with the Change name option makes problems
- [ ] next version with individual Search and Replace for the name option
- [ ] Display at TinyPNG how many KB were saved
- [ ] Solve problems when uploaded in site instead of page
