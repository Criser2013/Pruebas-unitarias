<?php
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversMethod(Holiday::class, "create")]
final class HolidayTest extends TestCase
{
    protected function setUp(): void
    {
        // Incluyendo archivos y configuraciones necesarias para la base de datos
        global $DOL_DOCUMENT_ROOT, $MAIN_DB_PREFIX;
        $DOL_DOCUMENT_ROOT = define("DOL_DOCUMENT_ROOT",dirname(__FILE__)."/../htdocs");
        $MAIN_DB_PREFIX = define("MAIN_DB_PREFIX","llx_");
        require_once dirname(__FILE__).'/../htdocs/core/db/mysqli.class.php';
        // Incluyendo la clase
        require_once dirname(__FILE__).'/../htdocs/holiday/class/holiday.class.php';
        // Funciones que se utilizan en la clase
        require_once dirname(__FILE__).'/../htdocs/core/lib/functions.lib.php';

        $this->db = $this->createMock(DoliDBMysqli::class);
        $this->db->method("begin")->willReturn(1);
        $this->db->method("commit")->willReturn(1);
        $this->db->method("rollback")->willReturn(1);
        $this->db->method("lasterror")->willReturn("Error");
        $this->db->method("escape")->willReturn("String limpio");
    }

    public static function trigger_provider(): array
    {
        return [[null, true],];
    }

    public static function no_trigger_provider(): array
    {
        return [[null, false],];
    }

    public static function null_trigger_provider(): array
    {
        return [[null, null],];
    }

    #[DataProvider('null_trigger_provider')] //ya
    public function test_create_1($user, $notrigger): void
    {
        $holiday = new Holiday($this->db);

        $holiday->fk_user = "";
        $holiday->fk_validator = null;
        $holiday->fk_type = null;
        $holiday->id = null;
        $holiday->ref = null;

        $this->db->expects($this->never())
        ->method("begin");

        $this->db->expects($this->never())
        ->method("query");

        $this->assertSame(-1, $holiday->create($user, $notrigger));
    }

    #[DataProvider('null_trigger_provider')] //ya
    public function test_create_2($user, $notrigger): void
    {
        $holiday = new Holiday($this->db);

        $holiday->fk_user = -1;
        $holiday->fk_validator = null;
        $holiday->fk_type = null;
        $holiday->id = null;
        $holiday->ref = null;

        $this->db->expects($this->never())
        ->method("begin");

        $this->db->expects($this->never())
        ->method("query");

        $this->assertSame(-1, $holiday->create($user, $notrigger));
    }

    #[DataProvider('trigger_provider')] //ya
    public function test_create_3($user, $notrigger): void
    {
        $holiday = new Holiday($this->db);

        $holiday->fk_user = 1;
        $holiday->fk_validator = 2;
        $holiday->fk_type = 3;
        $holiday->id = 6;
        $holiday->ref = "(PROV6)";

        $this->db->method("query")->willReturn(true);
        $this->db->method("last_insert_id")->willReturn(6);

        $this->db->expects($this->once())
        ->method("begin");

        $this->db->expects($this->exactly(2))
        ->method("query");

        $this->db->expects($this->never())
        ->method("lasterror");

        $this->db->expects($this->once())
        ->method("last_insert_id");

        $this->db->expects($this->once())
        ->method("commit");

        $this->db->expects($this->never())
        ->method("rollback");

        $this->assertSame(6, $holiday->create($user, $notrigger));
    }

    #[DataProvider('null_trigger_provider')] //ya
    public function test_create_4($user, $notrigger): void
    {
        $holiday = new Holiday($this->db);

        $holiday->fk_user = 1;
        $holiday->fk_validator = "";
        $holiday->fk_type = null;
        $holiday->id = null;
        $holiday->ref = null;

        $this->db->expects($this->never())
        ->method("query");

        $this->assertSame(-1, $holiday->create($user, $notrigger));
    }

    #[DataProvider('null_trigger_provider')] //ya
    public function test_create_5($user, $notrigger): void
    {
        $holiday = new Holiday($this->db);

        $holiday->fk_user = 1;
        $holiday->fk_validator = -1;
        $holiday->fk_type = null;
        $holiday->id = null;
        $holiday->ref = null;

        $this->db->expects($this->never())
        ->method("query");

        $this->assertSame(-1, $holiday->create($user, $notrigger));
    }

    #[DataProvider('null_trigger_provider')] //ya
    public function test_create_6($user, $notrigger): void
    {
        $holiday = new Holiday($this->db);

        $holiday->fk_user = 1;
        $holiday->fk_validator = 2;
        $holiday->fk_type = "";
        $holiday->id = null;
        $holiday->ref = null;

        $this->db->expects($this->never())
        ->method("query");

        $this->assertSame(-1, $holiday->create($user, $notrigger));
    }

    #[DataProvider('no_trigger_provider')] //ya
    public function test_create_7($user, $notrigger): void
    {
        $holiday = new Holiday($this->db);

        $holiday->fk_user = 1;
        $holiday->fk_validator = 2;
        $holiday->fk_type = -1;
        $holiday->id = null;
        $holiday->ref = null;

        $this->db->expects($this->never())
        ->method("query");

        $this->assertSame(-1, $holiday->create($user, $notrigger));
    }

    #[DataProvider('null_trigger_provider')] //ya
    public function test_create_8($user, $notrigger): void
    {
        $holiday = new Holiday($this->db);

        $holiday->fk_user = 1;
        $holiday->fk_validator = 2;
        $holiday->fk_type = 3;
        $holiday->id = null;
        $holiday->ref = null;

        $this->db->method("query")->willReturn(false);

        $this->db->expects($this->once())
        ->method("query");

        $this->db->expects($this->never())
        ->method("last_insert_id");

        $this->db->expects($this->once())
        ->method("begin");

        $this->db->expects($this->never())
        ->method("commit");

        $this->db->expects($this->once())
        ->method("rollback");

        $this->assertSame(-1, $holiday->create($user, $notrigger));
    }

    #[DataProvider('trigger_provider')] //ya - cuando se tiene id=0 falla
    public function test_create_9($user, $notrigger): void
    {
        $holiday = new Holiday($this->db);

        $holiday->fk_user = 1;
        $holiday->fk_validator = 2;
        $holiday->fk_type = 3;
        $holiday->id = 0;
        $holiday->ref = null;

        $this->db->method("query")->willReturn(true);
        $this->db->method("last_insert_id")->willReturn(0);

        $this->db->expects($this->once())
        ->method("query");

        $this->db->expects($this->once())
        ->method("last_insert_id");

        $this->db->expects($this->once())
        ->method("begin");

        $this->db->expects($this->never())
        ->method("commit");

        $this->db->expects($this->once(1))
        ->method("rollback");

        $this->assertSame(-1, $holiday->create($user, $notrigger));
    }

    #[DataProvider('trigger_provider')] //ya - la función guarda el registro aunque la referencias esté vacía
    public function test_create_10($user, $notrigger): void
    {
        $holiday = new Holiday($this->db);

        $holiday->fk_user = 1;
        $holiday->fk_validator = 2;
        $holiday->fk_type = 3;
        $holiday->id = 6;
        $holiday->ref = "";

        $this->db->method("query")->willReturn(true,0);
        $this->db->method("last_insert_id")->willReturn(6);

        $this->db->expects($this->exactly(2))
        ->method("query");

        $this->db->expects($this->once())
        ->method("last_insert_id");

        $this->db->expects($this->once())
        ->method("begin");

        $this->db->expects($this->never())
        ->method("commit");

        $this->db->expects($this->once(1))
        ->method("rollback");

        $this->assertSame(-1, $holiday->create($user, $notrigger));
    }

    #[DataProvider('trigger_provider')] //ya
    public function test_create_11($user, $notrigger): void
    {
        $holiday =  $this->getMockBuilder(Holiday::class)
        ->onlyMethods(['insertExtraFields'])
        ->setConstructorArgs([$this->db])
        ->getMock();

        $holiday->method('insertExtraFields')->willReturn(-1);

        $holiday->fk_user = 1;
        $holiday->fk_validator = 2;
        $holiday->fk_type = 3;
        $holiday->id = 6;
        $holiday->ref = "(PROV6)";

        $this->db->method("query")->willReturn(true);
        $this->db->method("last_insert_id")->willReturn(6);

        $this->db->expects($this->exactly(2))
        ->method("query");

        $this->db->expects($this->once())
        ->method("last_insert_id");

        $this->db->expects($this->once())
        ->method("begin");

        $this->db->expects($this->never())
        ->method("commit");

        $this->db->expects($this->once(1))
        ->method("rollback");

        $this->assertSame(-1, $holiday->create($user, $notrigger));
    }

    #[DataProvider('no_trigger_provider')] //ya
    public function test_create_12($user, $notrigger): void
    {
        $holiday = new Holiday($this->db);

        $holiday->fk_user = 1;
        $holiday->fk_validator = 2;
        $holiday->fk_type = 3;
        $holiday->id = 6;
        $holiday->ref = "(PROV6)";

        $this->db->method("query")->willReturn(true,false);
        $this->db->method("last_insert_id")->willReturn(6);

        $this->db->expects($this->exactly(2))
        ->method("query");

        $this->db->expects($this->once())
        ->method("last_insert_id");

        $this->db->expects($this->once())
        ->method("begin");

        $this->db->expects($this->once())
        ->method("commit");

        $this->db->expects($this->never())
        ->method("rollback");

        $this->assertSame(6, $holiday->create($user, $notrigger));
    }
}