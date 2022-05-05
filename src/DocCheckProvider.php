<?php

namespace RedSnapper\DocCheck;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RedSnapper\DocCheck\Exceptions\InvalidStateException;
use RedSnapper\DocCheck\Exceptions\LanguageDoesNotExistException;
use RedSnapper\DocCheck\Exceptions\TemplateDoesNotExistException;

class DocCheckProvider
{
    private const URL = 'https://login.doccheck.com/code/%s/%s/%s/%s';

    private string $template = "login_xl";

    private string $language = "com";

    private array $languages = ["de", "com", "fr", "it", "es", "nl", "frbe"];

    private array $templates = [
        "login_s" => ["width" => 156, 'height' => 203],
        "login_m" => ["width" => 311, 'height' => 188],
        "login_l" => ["width" => 424, 'height' => 215],
        "login_xl" => ["width" => 467, 'height' => 231],
    ];

    private ?DocCheckUser $user = null;

    private Request $request;

    public function __construct(private string $client_id, private ?string $client_secret = null)
    {

    }


    /**
     * @param  string  $code
     * @return $this
     * @throws \Throwable
     */
    public function language(string $code): self
    {

        throw_if(!in_array($code, $this->languages), LanguageDoesNotExistException::class);

        $this->language = $code;

        return $this;
    }

    /**
     * @param  string  $code
     * @return $this
     * @throws \Throwable
     */
    public function template(string $code): self
    {

        throw_if(!in_array($code, array_keys($this->templates)), TemplateDoesNotExistException::class);

        $this->template = $code;

        return $this;
    }


    public function iframeUrl(): string
    {

        $this->request->session()->put('state', $state = $this->getState());

        return sprintf(self::URL,
            $this->language,
            $this->client_id,
            $this->template,
            "session_id=".$state
        );
    }

    public function iframe(): string
    {
        $src = $this->iframeUrl();

        ['width'=>$width,'height'=>$height] = $this->templates[$this->template];


        return "<iframe width=\"$width\" height=\"$height\" name=\"dc_login_iframe\" id=\"dc_login_iframe\" src=\"$src\" ><a href=\"$src\" target=\"_blank\">LOGIN</a></iframe>";
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function user(): DocCheckUser
    {
        if ($this->user) {
            return $this->user;
        }

        if ($this->hasInvalidState()) {
            throw new InvalidStateException;
        }

        $this->user = new DocCheckUser($this->request->get('uniquekey'),
            collect($this->request->all())->filter(fn($value, $key) => Str::startsWith($key, 'dc_'))->all());

        return $this->user;

    }

    private function hasInvalidState(): bool
    {
        $state = $this->request->session()->pull('state');
        $session_id = $this->request->input('session_id');
        $session_id_enc = $this->request->input('session_id_enc');

        if (empty($state)) {
            return true;
        }

        if (is_null($this->client_secret)) {
            return $session_id !== $state;
        }

        if (empty($session_id_enc)) {
            return true;
        }

        return md5($session_id.$this->client_secret) !== $session_id_enc;
    }

    /**
     * Get the string used for session state.
     *
     * @return string
     */
    protected function getState()
    {
        return Str::random(40);
    }


}
