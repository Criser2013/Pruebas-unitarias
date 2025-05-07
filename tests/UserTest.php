<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversMethod(User::class, "setstatus")]
final class UserTest extends TestCase
{
    protected function setUp(): void
    {
        // Incluyendo archivos y configuraciones necesarias para la base de datos
        global $DOL_DOCUMENT_ROOT, $user;
        $DOL_DOCUMENT_ROOT = define("DOL_DOCUMENT_ROOT", dirname(__FILE__) . "/../htdocs");
        // Funciones que se utilizan en la clase
        require_once dirname(__FILE__) . '/../htdocs/core/lib/functions.lib.php';
        require_once dirname(__FILE__) . '/../htdocs/core/db/mysqli.class.php';
        // Incluyendo la clase
        require_once dirname(__FILE__) . '/../htdocs/user/class/user.class.php';

        $this->db = $this->createMock(DoliDBMysqli::class);
        $this->db->method("prefix")->willReturn("llx_doli_user_");
    }

    public static function null_statut_provider() {
        return [[null,],];
    }

    public static function statut_provider() {
        return [[1],];
    }

    public static function cero_statut_provider() {
        return [[0],];
    }

   #[DataProvider('statut_provider')]
    public function test_setstatus_1(int $statut): void
    {
        $user =  $this->getMockBuilder(User::class)
        ->onlyMethods(['call_trigger'])
        ->setConstructorArgs([$this->db])
        ->getMock();

        $user->statut = $statut;

        $user->expects($this->never())
        ->method('call_trigger');

        $this->db->expects($this->never())
        ->method("begin");

        $this->db->expects($this->never())
        ->method('commit');

        $this->db->expects($this->never())
        ->method('rollback');

        $this->assertSame(0, $user->setstatus($statut));
    }

    #[DataProvider('cero_statut_provider')]
    public function test_setstatus_2(int $statut): void
    {
        $user =  $this->getMockBuilder(User::class)
        ->onlyMethods(['call_trigger'])
        ->setConstructorArgs([$this->db])
        ->getMock();

        $user->method("call_trigger")->willReturn(1);
        $this->db->method("query")->willReturn(true);

        $user->statut = 3;
        $user->id = 123;
        $user->login = "TestUser";

        $user->expects($this->once())
        ->method('call_trigger');

        $this->db->expects($this->once())
        ->method("prefix");

        $this->db->expects($this->once())
        ->method("query");

        $this->db->expects($this->once())
        ->method("begin");

        $this->db->expects($this->once())
        ->method("commit");

        $this->db->expects($this->never())
        ->method("rollback");

        $this->assertSame(1, $user->setstatus($statut));
        $this->assertSame($statut, $user->statut);
        $this->assertSame($statut, $user->status);
        $this->assertSame("User TestUser disabled",$user->context["actionmsg"]);
    }

    #[DataProvider('statut_provider')]
    public function test_setstatus_3(int $statut): void
    {
        $user =  $this->getMockBuilder(User::class)
        ->onlyMethods(['call_trigger'])
        ->setConstructorArgs([$this->db])
        ->getMock();

        $user->method("call_trigger")->willReturn(1);
        $this->db->method("query")->willReturn(true);

        $user->statut = 3;
        $user->id = 123;
        $user->login = "TestUser";

        $user->expects($this->once())
        ->method('call_trigger');

        $this->db->expects($this->once())
        ->method("prefix");

        $this->db->expects($this->once())
        ->method("query");

        $this->db->expects($this->once())
        ->method("begin");

        $this->db->expects($this->once())
        ->method("commit");

        $this->db->expects($this->never())
        ->method("rollback");

        $this->assertSame(1, $user->setstatus($statut));
        $this->assertSame($statut, $user->statut);
        $this->assertSame($statut, $user->status);
        $this->assertSame("User TestUser enabled",$user->context["actionmsg"]);
    }

    #[DataProvider('statut_provider')]
    public function test_setstatus_4(int $statut): void
    {
        $user =  $this->getMockBuilder(User::class)
        ->onlyMethods(['call_trigger'])
        ->setConstructorArgs([$this->db])
        ->getMock();

        $this->db->method("query")->willReturn(false);

        $user->statut = 0;
        $user->id = 123;
        $user->login = "TestUser";

        $user->expects($this->never())
        ->method('call_trigger');

        $this->db->expects($this->once())
        ->method("prefix");

        $this->db->expects($this->once())
        ->method("query");

        $this->db->expects($this->once())
        ->method("begin");

        $this->db->expects($this->never())
        ->method("commit");

        $this->db->expects($this->once())
        ->method("rollback");

        $this->assertSame(-1, $user->setstatus($statut));
        $this->assertNotEquals($statut, $user->statut);
        $this->assertNotEquals($statut, $user->status);
    }

    #[DataProvider('statut_provider')]
    public function test_setstatus_5(int $statut): void
    {
        $user =  $this->getMockBuilder(User::class)
        ->onlyMethods(['call_trigger'])
        ->setConstructorArgs([$this->db])
        ->getMock();

        $user->method("call_trigger")->willReturn(-1);
        $this->db->method("query")->willReturn(true);

        $user->statut = 0;
        $user->id = 123;
        $user->login = "TestUser";

        $user->expects($this->once())
        ->method('call_trigger');

        $this->db->expects($this->once())
        ->method("prefix");

        $this->db->expects($this->once())
        ->method("query");

        $this->db->expects($this->once())
        ->method("begin");

        $this->db->expects($this->never())
        ->method("commit");

        $this->db->expects($this->once())
        ->method("rollback");

        $this->assertSame(-1, $user->setstatus($statut));
        $this->assertNotEquals($statut, $user->statut);
        $this->assertNotEquals($statut, $user->status);
    }

    #[DataProvider('statut_provider')]
    public function test_setstatus_6(int $statut): void
    {
        $user =  $this->getMockBuilder(User::class)
        ->onlyMethods(['call_trigger'])
        ->setConstructorArgs([$this->db])
        ->getMock();

        $user->statut = null;
        $user->status = 1;
        $user->id = 123;
        $user->login = "TestUser";

        $user->expects($this->never())
        ->method('call_trigger');

        $this->db->expects($this->never())
        ->method("prefix");

        $this->db->expects($this->never())
        ->method("query");

        $this->db->expects($this->never())
        ->method("begin");

        $this->db->expects($this->never())
        ->method("commit");

        $this->db->expects($this->never())
        ->method("rollback");

        $this->assertSame(0, $user->setstatus($statut));
    }

    #[DataProvider('cero_statut_provider')]
    public function test_setstatus_7(int $statut): void
    {
        $user =  $this->getMockBuilder(User::class)
        ->onlyMethods(['call_trigger'])
        ->setConstructorArgs([$this->db])
        ->getMock();

        $user->method("call_trigger")->willReturn(1);
        $this->db->method("query")->willReturn(true);

        $user->statut = null;
        $user->status = 3;
        $user->id = 123;
        $user->login = "TestUser";

        $user->expects($this->once())
        ->method('call_trigger');

        $this->db->expects($this->once())
        ->method("prefix");

        $this->db->expects($this->once())
        ->method("query");

        $this->db->expects($this->once())
        ->method("begin");

        $this->db->expects($this->once())
        ->method("commit");

        $this->db->expects($this->never())
        ->method("rollback");

        $this->assertSame(1, $user->setstatus($statut));
        $this->assertEquals($statut, $user->statut);
        $this->assertEquals($statut, $user->status);
        $this->assertSame("User TestUser disabled",$user->context["actionmsg"]);
    }

#[DataProvider('statut_provider')]
    public function test_setstatus_8(int $statut): void
    {
        $user =  $this->getMockBuilder(User::class)
        ->onlyMethods(['call_trigger'])
        ->setConstructorArgs([$this->db])
        ->getMock();

        $user->method("call_trigger")->willReturn(1);
        $this->db->method("query")->willReturn(true);

        $user->statut = null;
        $user->status = 0;
        $user->id = 123;
        $user->login = "TestUser";

        $user->expects($this->once())
        ->method('call_trigger');

        $this->db->expects($this->once())
        ->method("prefix");

        $this->db->expects($this->once())
        ->method("query");

        $this->db->expects($this->once())
        ->method("begin");

        $this->db->expects($this->once())
        ->method("commit");

        $this->db->expects($this->never())
        ->method("rollback");

        $this->assertSame(1, $user->setstatus($statut));
        $this->assertEquals($statut, $user->statut);
        $this->assertEquals($statut, $user->status);
        $this->assertSame("User TestUser enabled",$user->context["actionmsg"]);
    }
}