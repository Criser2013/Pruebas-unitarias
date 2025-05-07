<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversMethod(Contrat::class, "reopen")]
final class ContratTest extends TestCase
{
    protected function setUp(): void
    {
        // Incluyendo archivos y configuraciones necesarias para la base de datos
        global $DOL_DOCUMENT_ROOT;
        define("MAIN_DB_PREFIX","llx_");
        $DOL_DOCUMENT_ROOT = define("DOL_DOCUMENT_ROOT", dirname(__FILE__) . "/../htdocs");
        // Funciones que se utilizan en la clase
        require_once dirname(__FILE__) . '/../htdocs/core/lib/functions.lib.php';
        require_once dirname(__FILE__) . '/../htdocs/core/db/mysqli.class.php';
        // Incluyendo la clase
        require_once dirname(__FILE__) . '/../htdocs/contrat/class/contrat.class.php';
        require_once dirname(__FILE__) . '/../htdocs/user/class/user.class.php';

        $this->db = $this->createMock(DoliDBMysqli::class);
        $this->db->method("lasterror")->willReturn("Error");
        $this->db->method("prefix")->willReturn("llx_doli_contrat_");

        $this->user = $this->getMockBuilder(User::class)
        ->setConstructorArgs([$this->db])
        ->getMock();
    }

    public static function trigger_provider() {
        return [[1,], ];
    }

    public static function no_trigger_provider() {
        return [[0,], ];
    }

    #[DataProvider('no_trigger_provider')]
    public function test_reopen_1(int $notrigger): void
    {
        $contrat =  $this->getMockBuilder(Contrat::class)
        ->onlyMethods(['fetch_thirdparty', 'call_trigger'])
        ->setConstructorArgs([$this->db])
        ->getMock();

        $this->db->method("query")->willReturn(false);

        $contrat->id = 123;
        $contrat->statut = 1;

        $contrat->expects($this->never())
        ->method('call_trigger');

        $contrat->expects($this->once())
        ->method('fetch_thirdparty');

        $this->db->expects($this->once())
        ->method("begin");

        $this->db->expects($this->never())
        ->method('commit');

        $this->db->expects($this->once())
        ->method('rollback');

        $this->assertSame(-1, $contrat->reopen($this->user, $notrigger));
    }

    #[DataProvider('trigger_provider')]
    public function test_reopen_2(int $notrigger): void
    {
        $contrat =  $this->getMockBuilder(Contrat::class)
        ->onlyMethods(['fetch_thirdparty', 'call_trigger'])
        ->setConstructorArgs([$this->db])
        ->getMock();

        $contrat->method("fetch_thirdparty")->willReturn(1);

        $this->db->method("query")->willReturn(true);

        $contrat->id = 123;
        $contrat->statut = 1;

        $contrat->expects($this->never())
        ->method('call_trigger');

        $contrat->expects($this->once())
        ->method('fetch_thirdparty');

        $this->db->expects($this->once())
        ->method("begin");

        $this->db->expects($this->once())
        ->method('commit');

        $this->db->expects($this->never())
        ->method('rollback');

        $this->assertSame(1, $contrat->reopen($this->user, $notrigger));
        $this->assertSame(0, $contrat->statut);
        $this->assertSame(0, $contrat->status);
    }

    #[DataProvider('no_trigger_provider')]
    public function test_reopen_3(int $notrigger): void
    {
        $contrat =  $this->getMockBuilder(Contrat::class)
        ->onlyMethods(['fetch_thirdparty', 'call_trigger'])
        ->setConstructorArgs([$this->db])
        ->getMock();

        $contrat->method("fetch_thirdparty")->willReturn(1);
        $contrat->method("call_trigger")->willReturn(-1);

        $this->db->method("query")->willReturn(true);

        $contrat->id = 123;
        $contrat->statut = 1;

        $contrat->expects($this->once())
        ->method('call_trigger');

        $contrat->expects($this->once())
        ->method('fetch_thirdparty');

        $this->db->expects($this->once())
        ->method("begin");

        $this->db->expects($this->never())
        ->method('commit');

        $this->db->expects($this->once())
        ->method('rollback');

        $this->assertSame(-1, $contrat->reopen($this->user, $notrigger));
    }

    #[DataProvider('no_trigger_provider')]
    public function test_reopen_4(int $notrigger): void
    {
        $contrat =  $this->getMockBuilder(Contrat::class)
        ->onlyMethods(['fetch_thirdparty', 'call_trigger'])
        ->setConstructorArgs([$this->db])
        ->getMock();

        $contrat->method("fetch_thirdparty")->willReturn(1);
        $contrat->method("call_trigger")->willReturn(1);

        $this->db->method("query")->willReturn(true);

        $contrat->id = 123;
        $contrat->statut = 1;

        $contrat->expects($this->once())
        ->method('call_trigger');

        $contrat->expects($this->once())
        ->method('fetch_thirdparty');

        $this->db->expects($this->once())
        ->method("begin");

        $this->db->expects($this->once())
        ->method('commit');

        $this->db->expects($this->never())
        ->method('rollback');

        $this->assertSame(1, $contrat->reopen($this->user, $notrigger));
}
}