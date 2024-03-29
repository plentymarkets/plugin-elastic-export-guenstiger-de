# Release Notes for Elastic Export Guenstiger.de

## v1.0.18 (2022-06-13)

### Changed
- The plugin user guide was updated.

## v1.0.17 (2022-05-24)

### Changed
- UPDATE - Additional updates to ensure compatibility with PHP 8.

## v1.0.16 (2020-06-09)

### Changed
- Deleted the plugin description and inserted link to plentymarkets manual instead.

## v1.0.15 (2019-10-10)

### Fixed
- The user guide was updated (changed form of address, corrected broken links).

## v1.0.14 (2019-05-10)

### Fixed
- The specified shipping time was not exported correctly.

## v1.0.13 (2019-01-22)

### Changed
- An incorrect link in the user guide was corrected.

## v1.0.12 (2018-07-12)

### Changed
- An incorrect link in the user guide was corrected.

## v1.0.11 (2018-04-30)

### Changed
- Laravel 5.5 update.

## v1.0.10 (2018-04-05)

### Added
- The PriceHelper will now consider the new setting "Live currency conversion".

### Changed
- The class FiltrationService is responsible for the filtration of all variations.
- Preview images updated.

## v1.0.9 (2018-03-22)

### Changed
- Extended the plugin short description.

### Added
- The PriceHelper now considers the new setting **Retail price**.

## v1.0.7 (2017-09-26)

### Changed
- The user guide was updated.

## v1.0.6 (2017-07-18)

### Fixed
- The plugin Elastic Export is now required to use the plugin format GuenstigerDE.

## v1.0.5 (2017-05-29)

### Fixed
- An issue was fixed which caused elastic search to ignore the set referrers for the barcodes.

## v1.0.4 (2017-05-15)

### Fixed
- An issue was fixed which caused the variations not to be exported in the correct order.
- An issue was fixed which caused the export format to export texts in the wrong language.

## v1.0.3 (2017-05-03)

### Added
- The dependency to the Elastic Export plugin was added to the plugin.json.

### Changed
- Outsourced the stock filter logic to the Elastic Export plugin.

### Fixed
- Logs are now correctly translated.
- The array definitions of the result fields are now correctly defined for the KeyMutator.
- Stock is now correctly calculated.
- Price is now correctly calculated.

## v1.0.2 (2017-03-22)

### Fixed
- We now use a different value to get the image URLs for plugins working with elastic search.

## v1.0.1 (2017-03-14)

### Added
- Added marketplace name.

### Changed
- Updated plugin icons.

## v1.0.0 (2017-02-22)

### Added
- Added initial plugin files
