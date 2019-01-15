<?php

use Frozzare\Redirects\Redirects;
use Frozzare\Redirects\Rule;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class RedirectsTest extends TestCase
{
    public function testFile()
    {
        $rules = (new Redirects(__DIR__ . '/testdata/_redirects'))->parse()->rules();
        $this->assertRules($rules);
    }

    public function testMatch()
    {
        $r = (new Redirects)->parse("
            # Implicit 301 redirects
            /blog/my-post.php  /blog/my-post

            # Proxying
            /api/*  https://api.example.com/:splat  200

            # one value or the other.  Must match exactly!
            /path/* param1=:value1 /otherpath/:value1/:splat 301
        ");

        $tests = [
            'https://example.com/api/hej' => new Rule([
                'from' => '/api/*',
                'to' => 'https://api.example.com/:splat',
                'status' => 200,
            ]),
            'https://example.com/api/9202+ahe?foo=bar' => new Rule([
                'from' => '/api/*',
                'to' => 'https://api.example.com/:splat',
                'status' => 200,
            ]),
            '/blog/my-post.php' => new Rule([
                'from' => '/blog/my-post.php',
                'to' => '/blog/my-post'
            ]),
            '/path/foo?param1=bar' => new Rule([
                'from' => '/path/*',
                'to' => '/otherpath/:value1/:splat',
                'params' => [
                    'param1' => ':value1',
                ],
            ]),
            '/path/foo?param1=' => new Rule([
                'from' => '/path/*',
                'to' => '/otherpath/:value1/:splat',
                'params' => [
                    'param1' => ':value1',
                ],
            ]),
            '/path/foo?param1' => null,
            '/path/foo' => null,
        ];

        foreach ($tests as $url => $expectedRule) {
            $rule = $r->match($url);
            $this->assertEquals($expectedRule, $rule);
        }

        $rule = $r->match(new Request('GET', 'https://example.com/api/hej'));
        $this->assertEquals($rule, new Rule([
            'from' => '/api/*',
            'to' => 'https://api.example.com/:splat',
            'status' => 200,
        ]));
    }

    public function testParser()
    {
        $r = (new Redirects)->parse(file_get_contents(__DIR__ . '/testdata/_redirects'));

        $this->assertRules($r->rules());
    }

    public function assertRules(array $rules)
    {
        $expected = [
            new Rule([
                'from' => '/home',
                'to' => '/',
            ]),
            new Rule([
                'from' => '/blog/my-post.php',
                'to' => '/blog/my-post'
            ]),
            new Rule([
                'from' => '/news',
                'to' => '/blog',
            ]),
            new Rule([
                'from' => '/google',
                'to' => 'https://www.google.com',
            ]),
            new Rule([
                'from' => '/home',
                'to' => '/',
            ]),
            new Rule([
                'from' => '/my-redirect',
                'to' => '/',
                'status' => 302,
            ]),
            new Rule([
                'from' => '/pass-through',
                'to' => '/index.html',
                'status' => 200,
            ]),
            new Rule([
                'from' => '/ecommerce',
                'to' => '/store-closed',
                'status' => 404,
            ]),
            new Rule([
                'from' => '/api/*',
                'to' => 'https://api.example.com/:splat',
                'status' => 200,
            ]),
            new Rule([
                'from' => '/*',
                'to' => '/index.html',
                'status' => 200,
            ]),
            new Rule([
                'from' => '/app/*',
                'to' => '/app/index.html',
                'status' => 200,
                'force' => true,
            ]),
            new Rule([
                'from' => '/',
                'to' => '/something',
                'status' => 302,
                'params' => [
                    'foo' => 'bar',
                ],
            ]),
            new Rule([
                'from' => '/',
                'to' => '/something',
                'status' => 302,
                'params' => [
                    'foo' => 'bar',
                    'bar' => 'baz',
                ],
            ]),
            new Rule([
                'from' => '/store',
                'to' => '/blog/:id',
                'status' => 302,
                'params' => [
                    'id' => ':id',
                ],
            ]),
            new Rule([
                'from' => '/articles',
                'to' => '/posts/:tag/:id',
                'params' => [
                    'id' => ':id',
                    'tag' => ':tag',
                ],
            ]),
            new Rule([
                'from' => '/path/*',
                'to' => '/otherpath/:splat',
            ]),
            new Rule([
                'from' => '/path/*',
                'to' => '/otherpath/:value1/:splat',
                'params' => [
                    'param1' => ':value1',
                ],
            ]),
            new Rule([
                'from' => '/path/*',
                'to' => '/otherpath/:value2/:splat',
                'params' => [
                    'param2' => ':value2',
                ],
            ]),
            new Rule([
                'from' => '/path/*',
                'to' => '/otherpath/:value1/:value2/:splat',
                'params' => [
                    'param1' => ':value1',
                    'param2' => ':value2',
                ],
            ]),
            new Rule([
                'from' => '/',
                'to' => '/china',
                'status' => 302,
                'country' => [
                    'cn',
                    'hk',
                    'tw',
                ],
            ]),
            new Rule([
                'from' => '/',
                'to' => '/israel',
                'status' => 302,
                'country' => [
                    'il',
                ],
            ]),
            new Rule([
                'from' => '/china/*',
                'to' => '/china/zh-cn/:splat',
                'status' => 302,
                'language' => [
                    'zh',
                ],
            ]),
            new Rule([
                'from' => '/news/:year/:month/:date/:slug',
                'to' => '/blog/:year/:month/:date/:slug',
            ]),
            new Rule([
                'from' => '/',
                'to' => '/hello',
                'role' => [
                    'editor',
                ],
            ]),
        ];

        foreach ($expected as $index => $expectedRule) {
            $rule = $rules[$index];
            $this->assertEquals($expectedRule, $rule);
        }
    }

    public function testUrl() {
        $r = (new Redirects)->parse("
            # Implicit 301 redirects
            /blog/my-post.php  /blog/my-post

            # Proxying
            /api/*  https://api.example.com/:splat  200

            # one value or the other.  Must match exactly!
            /path/* param1=:value1 /otherpath/:value1/:splat 301
        ");

        $tests = [
            'https://example.com/api/hej' => 'https://api.example.com/hej',
            'https://example.com/api/9202+ahe?foo=bar' => 'https://api.example.com/9202+ahe?foo=bar',
            '/blog/my-post.php' => '/blog/my-post',
            '/path/foo?param1=bar' => '/otherpath/bar/foo',
            '/path/foo?param1=' => '/otherpath//foo',
            '/path/foo?param1' => null,
            '/path/foo?param1=bar&bar=foo' => '/otherpath/bar/foo',
            '/path/foo' => null,
        ];

        foreach ($tests as $input => $expected) {
            $this->assertSame($expected, $r->url($input));
        }

        $url = $r->url(new Request('GET', 'https://example.com/api/hej'));
        $this->assertSame('https://api.example.com/hej', $url);
    }
}
