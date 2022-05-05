<?php

namespace RedSnapper\DocCheck\Tests;

use Illuminate\Http\Request;
use RedSnapper\DocCheck\DocCheckProvider;
use RedSnapper\DocCheck\Exceptions\InvalidStateException;
use RedSnapper\DocCheck\Exceptions\LanguageDoesNotExistException;
use RedSnapper\DocCheck\Exceptions\TemplateDoesNotExistException;

class DocCheckProviderTest extends TestCase
{
    /** @test */
    public function can_get_iframe_url()
    {
        $provider = new DocCheckProvider(123);
        $request = $this->setRequestWithSession();
        $provider->setRequest($request);

        $url = $provider
            ->language("de")
            ->template("login_m")
            ->iframeUrl();

        $this->assertStringContainsString($request->session()->get('state'), $url);
        $this->assertStringContainsString(123, $url);
        $this->assertStringContainsString("/de/", $url);
        $this->assertStringContainsString("/login_m/", $url);
    }

    /** @test */
    public function throws_an_exception_if_language_doesnt_exist()
    {
        $provider = new DocCheckProvider(123);

        $this->expectException(LanguageDoesNotExistException::class);

        $provider->language("doesnt-exist");
    }

    /** @test */
    public function throws_an_exception_if_template_doesnt_exist()
    {
        $provider = new DocCheckProvider(123);

        $this->expectException(TemplateDoesNotExistException::class);

        $provider->template("doesnt-exist");
    }

    /** @test */
    public function can_generate_iframe_markup()
    {
        $provider = new DocCheckProvider(123);
        $provider->setRequest($this->setRequestWithSession());

        $iframe = $provider
            ->template("login_m")
            ->iframe();

        $this->assertStringContainsString("<iframe",$iframe);
        $this->assertStringContainsString("login.doccheck.com",$iframe);
        $this->assertStringContainsString("login_m",$iframe);
        $this->assertStringContainsString("width=\"311\"",$iframe);
    }


    /** @test */
    public function a_user_is_returned_from_doccheck()
    {
        $provider = new DocCheckProvider(123);
        $request = $this->setRequestWithSession([
            'session_id' => 'ABC', 'uniquekey' => 123,
            'dc_anrede' => 'Mr',
            'dc_gender' => 'm',
            'dc_titel' => 'Dr',
            'dc_vorname' => 'Joe',
            'dc_name' => 'Blow',
            'dc_strasse' => 'Test Street 123',
            'dc_land' => 'uk',
            'dc_language_id' => '90',
            'dc_beruf' => '123',
            'dc_fachgebiet' => '234',
            'dc_activity' => '456',
            'dc_email' => 'example@company com',
            'dc_address_type' => 1,
            'dc_agreement' => 1
        ], ['state' => 'ABC']);
        $provider->setRequest($request);

        $user = $provider->user();

        $this->assertEquals(123, $user->getId());
        $this->assertEquals("Mr", $user->getSalutation());
        $this->assertEquals("m", $user->getGender());
        $this->assertEquals("Dr", $user->getTitle());
        $this->assertEquals("Joe", $user->getFirstName());
        $this->assertEquals("Blow", $user->getLastName());
        $this->assertEquals("Test Street 123", $user->getStreet());
        $this->assertEquals("uk", $user->getCountryISOCode());
        $this->assertEquals("90", $user->getLanguageId());
        $this->assertEquals("123", $user->getProfessionId());
        $this->assertEquals("234", $user->getDisciplineId());
        $this->assertEquals("456", $user->getActivityId());
        $this->assertEquals("example@company com", $user->getEmail());
        $this->assertEquals("1", $user->getAddressTypeId());
        $this->assertTrue($user->isConfirmed());
    }

    /** @test */
    public function can_return_a_user_when_encoded_params_match()
    {
        $provider = new DocCheckProvider(123, "456");
        $session_encoded = md5("ABC456");
        $request = $this->setRequestWithSession([
            'session_id' => 'ABC',
            'session_id_enc' => $session_encoded,
            'uniquekey'=>'9845'
        ],['state' => 'ABC']);

        $provider->setRequest($request);

        $user = $provider->user();

        $this->assertEquals('9845',$user->getId());
    }

    /** @test */
    public function invalid_state_exception_is_thrown_if_state_doesnt_match()
    {
        $provider = new DocCheckProvider(123);
        $request = $this->setRequestWithSession(['session_id' => 'DEF'], ['state' => 'ABC']);
        $provider->setRequest($request);

        $this->expectException(InvalidStateException::class);

        $provider->user();
    }

    /** @test */
    public function invalid_state_exception_is_thrown_if_state_doesnt_match_but_encoded_params_dont_match()
    {
        $provider = new DocCheckProvider(123, 456);
        $request = $this->setRequestWithSession(['session_id' => 'ABC', 'session_id_enc' => 'NOT_ENCODED'],
            ['state' => 'ABC']);
        $provider->setRequest($request);
        $this->expectException(InvalidStateException::class);

        $provider->user();
    }

    protected function setRequestWithSession(array $requestData = [], $sessionData = []): Request
    {
        $request = new Request($requestData);
        $session = $this->app->make('session')->driver('array');
        $session->put($sessionData);
        $request->setLaravelSession($session);
        return $request;
    }
}