<?php
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversMethod(UserBankAccount::class, "")]
final class UserBankAccountTest extends TestCase
{
    protected function setUp(): void
    {
        // Incluyendo archivos y configuraciones necesarias para la base de datos
        global $DOL_DOCUMENT_ROOT;
        $DOL_DOCUMENT_ROOT = define("DOL_DOCUMENT_ROOT",dirname(__FILE__)."/../htdocs");
        require_once dirname(__FILE__).'/../htdocs/core/db/mysqli.class.php';
        // Incluyendo la clase
        require_once dirname(__FILE__).'/../htdocs/user/class/userbankaccount.class.php';
        // Funciones que se utilizan en la clase
        require_once dirname(__FILE__).'/../htdocs/core/lib/functions.lib.php';

        $this->db = $this->createMock(DoliDBMysqli::class);
        $this->db->method("begin")->willReturn(1);
        $this->db->method("commit")->willReturn(1);
        $this->db->method("rollback")->willReturn(1);
        $this->db->method("lasterror")->willReturn("Error");
        $this->db->method("escape")->willReturn("String limpio");
        $this->db->method("last_insert_id")->willReturn(10);
        $this->db->method("prefix")->willReturn("BANK");
        $this->db->method("affected_rows")->willReturn(1);
    }

    public static function trigger_provider(): array
    {
        return [[null, true],];
    }

    public static function no_trigger_provider(): array
    {
        return [[null, false],];
    }

    #[DataProvider('trigger_provider')]
    public function test_update_1($user, $notrigger): void
    {
        $user_bank_account = new UserBankAccount($this->db);

        $user_bank_account->id = 1;
        $user_bank_account->label = "Proveedor";

        $this->db->method("query")->willReturn(true);

        $this->db->expects($this->once())
        ->method("query");
        $this->db->expects($this->exactly(12))
        ->method("escape");
        $this->db->expects($this->never())
        ->method("lasterror");
        $this->db->expects($this->once())
        ->method("commit");
        $this->db->expects($this->never())
        ->method("rollback");

        $this->assertSame(1, $user_bank_account->update($user, $notrigger));
    }

    #[DataProvider('trigger_provider')]
    public function test_update_2($user, $notrigger): void
    {
        $user_bank_account = new UserBankAccount($this->db);

        $user_bank_account->id = 0;
        $user_bank_account->label = "Proveedor";

        $this->db->method("query")->willReturn(true);
        $this->db->expects($this->exactly(3))
        ->method("query");
        $this->db->expects($this->atMost(25))
        ->method("escape");
        $this->db->expects($this->never())
        ->method("lasterror");
        $this->db->expects($this->once())
        ->method("commit");
        $this->db->expects($this->never())
        ->method("rollback");
        
        $this->assertSame(10, $user_bank_account->update($user, $notrigger));
    }

    #[DataProvider('trigger_provider')]
    public function test_update_3($user, $notrigger): void
    {
        $user_bank_account = new UserBankAccount($this->db);

        $user_bank_account->id = 1;
        $user_bank_account->label = " ";

        $this->db->method("query")->willReturn(true);

        $this->db->expects($this->once())
        ->method("query");
        $this->db->expects($this->exactly(11))
        ->method("escape");
        $this->db->expects($this->never())
        ->method("lasterror");
        $this->db->expects($this->once())
        ->method("commit");
        $this->db->expects($this->never())
        ->method("rollback");

        $this->assertSame(1, $user_bank_account->update($user, $notrigger));
    }

    #[DataProvider('trigger_provider')]
    public function test_update_4($user, $notrigger): void
    {
        $user_bank_account = new UserBankAccount($this->db);

        $user_bank_account->id = 1;
        $user_bank_account->label = "Proveedor";

        $this->db->method("query")->willReturn(false);

        $this->db->expects($this->once())
        ->method("query");
        $this->db->expects($this->exactly(12))
        ->method("escape");
        $this->db->expects($this->once())
        ->method("lasterror");
        $this->db->expects($this->never())
        ->method("commit");
        $this->db->expects($this->once())
        ->method("rollback");

        $this->assertSame(-1, $user_bank_account->update($user, $notrigger));
    }

    #[DataProvider('no_trigger_provider')]
    public function test_update_5($user, $notrigger): void
    {
        $user_bank_account =  $this->getMockBuilder(UserBankAccount::class)
        ->onlyMethods(['call_trigger'])
        ->setConstructorArgs([$this->db])
        ->getMock();

        $user_bank_account->id = 2;
        $user_bank_account->label = "Proveedor";

        $this->db->method("query")->willReturn(true);
        $user_bank_account->method('call_trigger')->willReturn(1);

        $user_bank_account->expects($this->once())
        ->method("call_trigger");
        $this->db->expects($this->once())
        ->method("query");
        $this->db->expects($this->exactly(12))
        ->method("escape");
        $this->db->expects($this->never())
        ->method("lasterror");
        $this->db->expects($this->once())
        ->method("commit");
        $this->db->expects($this->never())
        ->method("rollback");

        $this->assertSame(2, $user_bank_account->update($user, $notrigger));
    }

    #[DataProvider('no_trigger_provider')]
    public function test_update_6($user, $notrigger): void
    {
        $user_bank_account =  $this->getMockBuilder(UserBankAccount::class)
        ->onlyMethods(['call_trigger'])
        ->setConstructorArgs([$this->db])
        ->getMock();

        $user_bank_account->id = 3;
        $user_bank_account->label = "Proveedor";

        $this->db->method("query")->willReturn(true);
        $user_bank_account->method('call_trigger')->willReturn(-1);

        $user_bank_account->expects($this->once())
        ->method("call_trigger");
        $this->db->expects($this->once())
        ->method("query");
        $this->db->expects($this->exactly(12))
        ->method("escape");
        $this->db->expects($this->never())
        ->method("lasterror");
        $this->db->expects($this->never())
        ->method("commit");
        $this->db->expects($this->once())
        ->method("rollback");

        $this->assertSame(-1, $user_bank_account->update($user, $notrigger));
    }
}