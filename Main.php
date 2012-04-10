<?php

require_once 'FileSystemUtil.php';

use FileSystemUtil as FS;

class Main {

  private static $destinationPath;

  private function __construct() {}

  public static function run( $sourcePath, $destinationPath, $navXmlFileName ) {
    self::$destinationPath = $destinationPath;
    if( FS::isDir( $destinationPath ) ) {
      FS::removeDirRecursively( $destinationPath );
    }
    FS::createDir( $destinationPath );
    self::processNavigationXml( $navXmlFileName );
    self::processHelpDirectory( $sourcePath );
  }

  private static function processNavigationXml( $fileName ) {
    $file = FS::createFile( self::$destinationPath . "/navigation.php", FS::WRITE_ONLY );
    $xmlIterator = new SimpleXMLIterator( $fileName, null, true );
    self::createNavigation( $file, $xmlIterator );
  }

  private static function createNavigation( $file, $xmlIterator ) {
    $file -> fwrite( "<ul>" );
    foreach( $xmlIterator as $element ) {
      if( $element[ 'href' ] ) {
        $url = str_replace( "help/html/", "developers-guide/", $element[ 'href' ] );
        $file -> fwrite( "<li><a href='" . $url . "'>" . $element[ 'label' ] . "</a></li>" );
      } else {
        $file -> fwrite( "<li class='category-group'>" . $element[ 'label' ] . "</li>" );
      }
      if( $element -> count() > 0 ) {
        $file -> fwrite( "<li>" );
        self::createNavigation( $file, $element );
        $file -> fwrite( "</li>" );
      }
    }
    $file -> fwrite( "</ul>" );
  }

  private static function processHelpDirectory( $dirPath ) {
    FS::createDir( self::$destinationPath . "/" . $dirPath );
    $iterator = new FilesystemIterator( $dirPath );
    foreach( $iterator as $fileInfo ) {
      if( $fileInfo -> getExtension() == "html" ) {
        self::handleHtmlFile( $fileInfo );
      } else if( $fileInfo -> isDir() ) {
        self::processHelpDirectory( $fileInfo -> getPathname() );
      }
    }
  }

  private static function handleHtmlFile( $fileInfo ) {
    $fileContent = self::readFileContent( $fileInfo );
    $bodyContent = self::stripHtmlBodyContent( $fileContent );
    $newFilePath = self::$destinationPath . "/" . $fileInfo -> getPathname();
    self::writeInNewFile( $newFilePath, $bodyContent );
  }

  private static function readFileContent( $fileInfo ) {
    $result = "";
    $file = $fileInfo -> openFile();
    foreach( $file as $line ) {
      $result .= $line;
    }
    return $result;
  }

  private static function stripHtmlBodyContent( $fileContent ) {
    preg_match("|<body\b[^>]*>(.*?)</body>|s", $fileContent, $matches );
    return $matches[ 1 ];
  }

  private static function writeInNewFile( $filePath, $fileContent ) {
    $htmlFile = FS::createFile( $filePath, FS::WRITE_ONLY );
    $htmlFile -> fwrite( $fileContent );
  }

}

?>