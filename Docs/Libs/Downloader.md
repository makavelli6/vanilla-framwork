# Downloadable Class Library
--
### Usage:
- Basic file download :

``` php

			$file_path = 'files/my_file.rar';
			( new Downloader( $file_path ) )->download();
```

- Advanced file download
``` php
			( new Downloader( $file_path, Downloader::DOWNLOAD_FILE ) ) // Download file in File mode
				->setDownloadName( 'new_name' )							// Change download file name
				->resumable( false ) 									// turn off resumable capability
				->speedLimit( 200 )										// Limit download speed in kbyte/sec
				->authenticate( 'login_name', 'login_password' )		// authenticate before downloading
				->recordDownloaded( true )								// Used bandwith counter
				->autoExit( true )										// Auto exit after download completed
				->download();											// Start Download Process

```


### Documentation
___

**Downloader::speedLimit( int $speed )**
	$speed is integer represent downlowd speed in Kilobytes per second	
> __Note :__

> Speed Limiting depends on micro sleep,
So, Becarefull while using speed limit, in any value it works very well and almost accurate in local host testing.
in real host you should test to know the best safe minimum speed because in some hosts speed limit may cause download corrupt as server may close connection due to script sleeping

> Limited speed is defined for one connection, so while using resume and downloading with download programs such as IDM, there eill be multi connections, every single connection will download with defined speed, so overall speed will be many doubles of defined speed.

```php
	// File Mode
	( new Downloader( 'files/file.mp3' ) )
		->setDownloadName( 'newname.mp4' )
		->download();						// Downloaded file name will be newname.mp3,  mp4 will be ignored


	// Data Mode
	( new Downloader( 'files/file.mp3', Downloader::DOWNLOAD_DATA ) )
		->setDownloadName( 'newname.mp4' )
		->download();						// Downloaded file name will be newname.mp4, and couldn't be opened due to wrong extension, So becareful while downloading in data mode
```
--

**Downloader::setDownloadName( string $filename )**
	change downloaded file name
> this method behaviour depends also on download mode :
> if new extenstion supplied :
> in file mode ===> only file name will be changed and extension will be ignored .
> in data mode ===> file name and type will be change .
```php
			// assuming original file is 'file.mp3'
			// Download in File Mode
			$downloader->setDownloadName( 'another_name.avi' ); //  another_name.mp3
			// Download in Data Mode
			$downloader->setDownloadName( 'another_name.avi' ); // another_name.avi
			$downloader->setDownloadName( 'another_name' ); // another_name.mp3 ( if file.mp3 exists )
			 												// another_name.txt ( if file.mp3 doesn't exists )
```

--
**Downloader::resumable( bool $bool )**
	turn on or off resume capability
> Downloader Default behaviour is to use resume

```php	
			$downloader->resumable();  		// turn on

			$downloader->resumable( true ); // turn on

			$downloader->resumable( false ); // Turn off
```

--

**Downloader::speedLimit( int $speed )**
	$speed is integer represent downlowd speed in Kilobytes per second	
> __Note :__

> Speed Limiting depends on micro sleep,
> So, Becarefull while using speed limit, in any value it works very well and almost accurate in local host testing.
> in real host you should test to know the best safe minimum speed because in some hosts speed limit may cause download corrupt as server may close connection due to script sleeping

> Limited speed is defined for one connection, so while using resume and downloading with download programs such as IDM, there eill be multi connections, every single connection will download with defined speed, so overall speed will be many doubles of defined speed.

```php
			// Limit speed to 100 kB/s
			$downloader->speedLimit( 100 );
```