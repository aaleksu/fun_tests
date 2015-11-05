#!/usr/bin/php

<?php

// require_once __DIR__ . '/bootstrap.php';
$curdir = getcwd();
$defaultAutloadFilePath = $curdir . '/autoload.php';

$options = getopt('', ['autoload:']);
// var_dump($options);
// die();

if(file_exists($defaultAutloadFilePath)) {
    require_once $defaultAutloadFilePath;
}
else {
    if(empty($options['autoload'])) {
        exit(sprintf("\nSpecify autoload path with --autoload option like this: --autoload=<path>\n\n"));
    }

    $autoloadFilePath = $curdir . '/' . $options['autoload'] . 'autoload.php';
    if(file_exists($autoloadFilePath)) {
        require_once $autoloadFilePath;
    }
    else {
        exit(sprintf("\nFile %s does not exist, sorry. Check out path you passed into autload option\n\n"));
    }
}

use ForFun\FunTests\Base\Traits\FunTestOutputTrait;

class TestRunner
{
    use FunTestOutputTrait;

    private $argv;
    private $stopOnFail = false;
    private $testFile;
    private $testFiles = [];
    private $settings;
    private $runAll;
    private $curDir;

    public function __construct(array $argv = [])
    {
        $this->curDir = getcwd();
        try {
            if(file_exists($this->curDir . '/funtest.yml')) {
                if(function_exists('yaml_parse_file')) {
                    // yaml_parse_file() requires pecl extension :(bueeeee)
                    $this->settings = @yaml_parse_file($this->curDir . '/funtest.yml');
                    if(!$this->settings) {
                        throw new \Exception('Cannot parse funtest.yml. Make sure it has proper format');
                    }
                }
            }
            $options = getopt('a', ['stop-on-fail', 'all']);
            $this->stopOnFail = array_key_exists('stop-on-fail', $options);

            $this->runAll = array_key_exists('all', $options);

            $this->argv = $argv;
            $this->parseAndValidateArgv();

            $this->defineFilesToTest();
        }
        catch(\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function run()
    {
        try {
            foreach($this->testFiles as $testFile) {
                $this->runFile($testFile);
            }
        }
        catch(\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    private function runFile($testFile)
    {
        //if(file_exists($testFile)) {
            $this->log(sprintf("Ok. Will require %s file in a while", $testFile));
            $this->ok('Ok');
            
            $classPathinfo = pathinfo($testFile);
            $className = '\\' . str_replace('/', '\\', $classPathinfo['dirname']) . '\\' . $classPathinfo['filename'];
            //var_dump($className);
            //die();
            $testInstance = new $className();
            $testInstance->run($this->stopOnFail);
        // }
        // else {
        //     throw new \Exception('File does not exist ' . $this->argv[1]);
        // }
    }

    private function parseAndValidateArgv()
    {
        if($this->runAll) {
            return;
        }

        try {
            $argvLen = count($this->argv);
            if($argvLen == 1) {
                throw new \Exception('Missing argument: test file. Either specify test file or give me an option to test all (and specify funtest file then)');
            }

            foreach(range(1, $argvLen - 1) as $argvIndex) {
                if(preg_match('/\.php$/', $this->argv[$argvIndex])) {
                    $this->testFiles[] = $this->argv[$argvIndex];
                }
                else {
                    continue;
                    //throw new \Exception(sprintf('%s does no t seem to be a php file - check it put', $this->argv[$argvIndex]));
                }
            }

            if(empty($this->testFiles)) {
                throw new \Exception('Missing argument: test file. Either specify test file or give me an option to test all (and specify funtest file then)');
            }

            // if($argvLen > 2) {
            //  throw new \Exception('Too many arguments');
            // }
        }
        catch(\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    private function defineFilesToTest()
    {
        if(!$this->runAll) {
            return;
        }

        if(!isset($this->settings['cases'])) {
            throw new \Exception('Please specify files list to test');
        }

        if(!is_array($this->settings['cases'])) {
            throw new \Exception('Wrong settings format: cases should be an array');
        }

        foreach($this->settings['cases'] as $path) {
            if(file_exists($path)) {
                $testFiles = glob($path . $this->buildGlobPattern() . '*Test.php', GLOB_BRACE);

                foreach ($testFiles as $testFile) {
                    $this->testFiles[] = str_replace('//', '/', $testFile);
                }
            }
        }
    }

    private function buildGlobPattern($deepLevel = 8)
    {
        $path = [''];
        for($i = 1; $i <= $deepLevel; $i++) {
            $path[] = sprintf('/%s', str_repeat('*/', $i));
        }

        $path = join(',', $path);
        $path = sprintf('{%s}', $path);

        return $path;
        //'{,/*/,/*/*/,/*/*/*/,/*/*/*/*/,/*/*/*/*/*/}'
    }
}

$testRunner = new TestRunner($argv);
$testRunner->run();