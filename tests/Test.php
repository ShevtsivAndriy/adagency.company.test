<?php

use PHPUnit\Framework\TestCase;

require 'src/SimpleQueryBuilder.php';

class Test extends TestCase
{
    protected $query;

    protected function setUp(): void
    {
        parent::setUp();

        $this->query = new SimpleQueryBuilder();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->query = null;
    }

    public function test_is_query_valid()
    {
        $this->assertEquals(
            "SELECT id, email, password FROM user WHERE email like \"%andriy%\", password IS NOT NULL ORDER BY email LIMIT 15 OFFSET 5",
            $this->query->select(['id', 'email', 'password'])
                ->from('user')
                ->where(['email like "%andriy%"', 'password IS NOT NULL'])
                ->orderBy('email')
                ->limit(15)
                ->offset(5)
                ->build()
        );
    }

    public function test_valid_query_when_select_is_array()
    {
        $this->assertEquals(
            "SELECT id, name FROM users",
            $this->query->select(['id', 'name'])
                ->from('users')
                ->build()
        );
    }

    public function test_valid_query_when_select_is_string()
    {
        $this->assertEquals(
            "SELECT name FROM users",
            $this->query->select('name')
                ->from('users')
                ->build()
        );
    }

    public function test_valid_query_when_from_is_string()
    {
        $this->assertEquals(
            "SELECT * FROM users",
            $this->query->select('*')
                ->from('users')
                ->build()
        );
    }

    public function test_valid_query_when_from_is_array_of_string_and_sub_query()
    {
        $subQuery = new SimpleQueryBuilder();
        $subQuery->select('name')
            ->from('users');

        $this->assertEquals(
            "SELECT * FROM table1, (SELECT name FROM users) WHERE table1.user_id = users.id, table1.value > 125",
            $this->query->select('*')
                ->from([
                    'table1',
                    $subQuery
                ])
                ->where([
                    'table1.user_id = users.id',
                    'table1.value > 125'
                ])
                ->build()
        );
    }

    public function test_handle_exception()
    {
        $this->expectException(LogicException::class);
        $this->query->select('*')
            ->build();
    }

    public function test_sub_query()
    {
        $subQuery = new SimpleQueryBuilder();
        $subQuery->select('*')
            ->from('users')
            ->where('email IS NOT NULL');

        $this->assertEquals(
            "SELECT * FROM (SELECT * FROM users WHERE email IS NOT NULL) WHERE email like \"%andriy%\", active = 1",
            $this->query->select('*')
                ->from($subQuery)
                ->where(['email like "%andriy%"', 'active = 1'])
                ->build()
        );
    }

    public function test_valid_build_count()
    {
        $this->assertEquals(
            "COUNT (SELECT name FROM users WHERE name like \"%andriy%\")",
            $this->query->select('name')
                ->from('users')
                ->where('name like "%andriy%"')
                ->buildCount()
        );
    }
}
