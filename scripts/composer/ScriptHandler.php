<?php
namespace SfphpProject\composer;

use Composer\Script\Event;
use Composer\Semver\Comparator;

class ScriptHandler {

  public static function createRequiredFiles(Event $event) {
    $dirs = [
      'app/controllers',
      'app/models',
      'app/views',
      'etc/cache',
      'etc/config',
      'pub/logs',
      'pub/html'
    ];

    foreach ($dirs as $dir) {
      self::createDir($dir);
    }
  }

  public static function createDir($pathname, $mode = 0750)
  {
    if (is_dir($pathname) || empty($pathname)) {
      return true;
    }
    $pathname = str_replace(array('/', ''), DIRECTORY_SEPARATOR, $pathname);
    if (is_file($pathname)) {
      trigger_error('mkdirr() File exists', E_USER_WARNING);
      return false;
    }
    $next_pathname = substr($pathname, 0, strrpos($pathname, DIRECTORY_SEPARATOR));
    if (self::createDir($next_pathname, $mode)) {
      if (!file_exists($pathname)) {
        return mkdir($pathname, $mode);
      }
    }
    return false;
  }

  public static function checkComposerVersion(Event $event) {
    $composer = $event->getComposer();
    $io = $event->getIO();
    $version = $composer::VERSION;

    if (preg_match('/^[0-9a-f]{40}$/i', $version)) {
      $version = $composer::BRANCH_ALIAS_VERSION;
    }

    if ($version === '@package_version@' || $version === '@package_branch_alias_version@') {
      $io->writeError('<warning>You are running a development version of Composer. If you experience problems, please update Composer to the latest stable version.</warning>');
    }
    elseif (Comparator::lessThan($version, '1.0.0')) {
      $io->writeError('<error>Sfphp-project requires Composer version 1.0.0 or higher. Please update your Composer before continuing</error>.');
      exit(1);
    }
  }

}