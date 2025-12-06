<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Tests\Session;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * SessionTest.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Robert Schönthal <seroscho@googlemail.com>
 * @author Drak <drak@zikula.org>
 */
class SessionTest extends TestCase
{
    /**
     * @var \Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface
     */
    protected $storage;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    protected $session;

    protected function setUp()
    {
        $this->storage = new MockArraySessionStorage();
        session() = new Session($this->storage, new AttributeBag(), new FlashBag());
    }

    protected function tearDown()
    {
        $this->storage = null;
        session() = null;
    }

    public function testStart()
    {
        $this->assertEquals('', session()->getId());
        $this->assertTrue(session()->start());
        $this->assertNotEquals('', session()->getId());
    }

    public function testIsStarted()
    {
        $this->assertFalse(session()->isStarted());
        session()->start();
        $this->assertTrue(session()->isStarted());
    }

    public function testSetId()
    {
        $this->assertEquals('', session()->getId());
        session()->setId('0123456789abcdef');
        session()->start();
        $this->assertEquals('0123456789abcdef', session()->getId());
    }

    public function testSetName()
    {
        $this->assertEquals('MOCKSESSID', session()->getName());
        session()->setName('session.test.com');
        session()->start();
        $this->assertEquals('session.test.com', session()->getName());
    }

    public function testGet()
    {
        // tests defaults
        $this->assertNull(session()->get('foo'));
        $this->assertEquals(1, session()->get('foo', 1));
    }

    /**
     * @dataProvider setProvider
     */
    public function testSet($key, $value)
    {
        session()->set($key, $value);
        $this->assertEquals($value, session()->get($key));
    }

    /**
     * @dataProvider setProvider
     */
    public function testHas($key, $value)
    {
        session()->set($key, $value);
        $this->assertTrue(session()->has($key));
        $this->assertFalse(session()->has($key.'non_value'));
    }

    public function testReplace()
    {
        session()->replace(array('happiness' => 'be good', 'symfony' => 'awesome'));
        $this->assertEquals(array('happiness' => 'be good', 'symfony' => 'awesome'), session()->all());
        session()->replace(array());
        $this->assertEquals(array(), session()->all());
    }

    /**
     * @dataProvider setProvider
     */
    public function testAll($key, $value, $result)
    {
        session()->set($key, $value);
        $this->assertEquals($result, session()->all());
    }

    /**
     * @dataProvider setProvider
     */
    public function testClear($key, $value)
    {
        session()->set('hi', 'fabien');
        session()->set($key, $value);
        session()->clear();
        $this->assertEquals(array(), session()->all());
    }

    public function setProvider()
    {
        return array(
            array('foo', 'bar', array('foo' => 'bar')),
            array('foo.bar', 'too much beer', array('foo.bar' => 'too much beer')),
            array('great', 'symfony is great', array('great' => 'symfony is great')),
        );
    }

    /**
     * @dataProvider setProvider
     */
    public function testRemove($key, $value)
    {
        session()->set('hi.world', 'have a nice day');
        session()->set($key, $value);
        session()->remove($key);
        $this->assertEquals(array('hi.world' => 'have a nice day'), session()->all());
    }

    public function testInvalidate()
    {
        session()->set('invalidate', 123);
        session()->invalidate();
        $this->assertEquals(array(), session()->all());
    }

    public function testMigrate()
    {
        session()->set('migrate', 321);
        session()->migrate();
        $this->assertEquals(321, session()->get('migrate'));
    }

    public function testMigrateDestroy()
    {
        session()->set('migrate', 333);
        session()->migrate(true);
        $this->assertEquals(333, session()->get('migrate'));
    }

    public function testSave()
    {
        session()->start();
        session()->save();
    }

    public function testGetId()
    {
        $this->assertEquals('', session()->getId());
        session()->start();
        $this->assertNotEquals('', session()->getId());
    }

    public function testGetFlashBag()
    {
        $this->assertInstanceOf('Symfony\\Component\\HttpFoundation\\Session\\Flash\\FlashBagInterface', session()->getFlashBag());
    }

    public function testGetIterator()
    {
        $attributes = array('hello' => 'world', 'symfony' => 'rocks');
        foreach ($attributes as $key => $val) {
            session()->set($key, $val);
        }

        $i = 0;
        foreach (session() as $key => $val) {
            $this->assertEquals($attributes[$key], $val);
            ++$i;
        }

        $this->assertEquals(count($attributes), $i);
    }

    public function testGetCount()
    {
        session()->set('hello', 'world');
        session()->set('symfony', 'rocks');

        $this->assertCount(2, session());
    }

    public function testGetMeta()
    {
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Storage\MetadataBag', session()->getMetadataBag());
    }
}
