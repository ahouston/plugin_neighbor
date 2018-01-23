# Cacti CLI Script - add_site.php

The add_site.php cli script can be used to:

* Add new sites to Cacti
* Map devices to existing sites
* Update values such as GPS coordinates
* Geocode addresses using the Google APIs.

It is released along with the Cacti Neighbor plugin as the mapping functionality works better when the seed position is based on a GPS coordinate for devices.

## Usage

**Usage: php add_site.php [options] **

### Site options:
```
    --name=[Site Name]          e.g. 'Telehouse East'
    --addr1=[Address Line 1]    e.g. 'Coriander Road'
    --addr2=[Address Line 2]    e.g. 'Poplar'
    --city=[City]               e.g. 'London''
    --state=[State]             e.g. 'London'
    --postcode=[Zip or Postcode]        e.g. 'E14 2AA'
    --country=[Country]         e.g. 'United Kingdom'
    --timezone=[Timezone]       e.g. 'Europe/London'
    --latitude=[Latitutude]     e.g. '51.5115172'
    --longitude=[Longitude]     e.g. '-0.0017868'
    --alt-name=[Alt. Name]      e.g. 'LINX Telehouse'
    --notes=[Site Notes]        e.g. 'Email: support@telehouse.net'
```
### Geocoding Options:
Get and API key from: **https://developers.google.com/maps/documentation/geocoding/get-api-key**
```
    --geocode   Try to turn addresses into GPS coordinates
    --geocode-api-key Your Google API key
    --proxy     Proxy server to use in http://proxy.server:port format

```
### Device Map Options:
```
    --device-map-regex=[regular expression]     e.g.'rtr-th[e|w]-pe\d'
    --device-map-wildcard=[mysql like]  e.g.'rtr-%the%-pe%'

    --ip-map-regex=[regular expression] e.g. '172.31.224.[1-8]'
    --ip-map-wildcard=[mysql like]      e.g.'172.31.224.%'
    --do-map    Do the mapping.
```
### General Options:
```
    --quiet             Keep it quiet
    --no-replace        Allow duplicate site names to be created
```
## Notes

By default, sites with the same name will be updated rather than added.
This can be disabled with --no-replace

GPS coordinates should preferably be in dotted decimal format,
if supplied in DMS format, a conversion will be attempted, but
your mileage may vary.

Devices can be mapped to the site by providing either regular expression
or MySQL wildcard against the host description or IP address.

By default, only matching devices will be shown, to actually make
the changes, use the --do-map option. This is to mistaken updates,
please check your filters work first!

There are some macros which will be expanded in the --notes field:

* **%DATE%** - The current date in mysql format
* **%TIME%** - The current time in mysql format
* **%GOOGLE_MAPS_URL%** - The link to Google Maps for this sites GPS coordinates

## Bugs and Feature Enhancements
   
Bug and feature enhancements for the neighbor plugin are handled in GitHub.
All reasonable feature requests will be entertained!

## ChangeLog

--- 0.1 ---
* Preliminary Commit
