<?php

class FileSystemUtil {

  const READ_ONLY = 'r';
  const READ_WRITE = 'r+';
  const WRITE_ONLY = 'w';

  private function __construct() {}

  public static function isDir( $directoryPath ) {
    return is_dir( $directoryPath );
  }

  public static function isDirEmpty( $directoryPath ) {
    $iterator = new RecursiveDirectoryIterator( $directoryPath );
    return $iterator -> hasChildren();
  }

  public static function createFile( $filePath, $openMode ) {
    $handle = fopen( $filePath, $openMode );
    fclose( $handle );
    return new SplFileObject( $filePath, $openMode );
  }

  public static function removeFile( $filePath ) {
    unlink( $filePath );
  }

  public static function createDir( $directoryPath ) {
    mkdir( $directoryPath );
  }

  public static function removeDir( $directoryPath ) {
    rmdir( $directoryPath );
  }

  public static function removeDirRecursively( $directoryPath ) {
    $iterator = new FilesystemIterator( $directoryPath );
    foreach( $iterator as $fileInfo ) {
      if( $fileInfo -> isDir() ) {
        self::removeDirRecursively( $fileInfo -> getPathname() );
      } else if( $fileInfo -> isFile() ) {
        self::removeFile( $fileInfo -> getPathname() );
      }
    }
    self::removeDir( $directoryPath );
  }

}

?>