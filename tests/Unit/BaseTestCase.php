<?php

namespace Tests\Unit {

    use Mockery;
    use Mockery\Adapter\Phpunit\MockeryTestCase;

    abstract class BaseTestCase extends MockeryTestCase
    {
        public $SUT;
        public static $env;
        public static $response;

        protected function mockeryTestSetUp()
        {
            parent::mockeryTestSetUp();
            self::$env = env('APP_ENV');
            self::$response = Mockery::mock('App\Http\Response');
        }
    }
}
