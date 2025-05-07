<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversMethod(Ticket::class, "setProgression")]
final class TicketTest extends TestCase
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
        require_once dirname(__FILE__) . '/../htdocs/ticket/class/ticket.class.php';

        $this->db = $this->createMock(DoliDBMysqli::class);
    }

    public static function percent_provider() {
        return [[75],];
    }

    public static function cero_percent_provider() {
        return [[0],];
    }


   #[DataProvider('percent_provider')]
    public function test_setProgression_1(int $percent): void
    {
        $ticket =  new Ticket($this->db);

        $ticket->id = null;

        $this->db->expects($this->never())
        ->method("query");

        $this->assertSame(-1, $ticket->setProgression($percent));
    }

    #[DataProvider('percent_provider')]
    public function test_setProgression_2(int $percent): void
    {
        $ticket =  new Ticket($this->db);

        $this->db->method("query")->willReturn(true);

        $ticket->id = 123;

        $this->db->expects($this->once())
        ->method("query");

        $this->assertSame(1, $ticket->setProgression($percent));
    }

    #[DataProvider('cero_percent_provider')]
    public function test_setProgression_3(int $percent): void
    {
        $ticket =  new Ticket($this->db);

        $this->db->method("query")->willReturn(true);

        $ticket->id = 123;

        $this->db->expects($this->once())
        ->method("query")
        ->with("UPDATE ".MAIN_DB_PREFIX."ticket SET progress = null WHERE rowid = 123");

        $this->assertSame(1, $ticket->setProgression($percent));
    }

    #[DataProvider('percent_provider')]
    public function test_setProgression_4(int $percent): void
    {
        $ticket =  new Ticket($this->db);

        $this->db->method("query")->willReturn(false);

        $ticket->id = 123;

        $this->db->expects($this->once())
        ->method("query")
        ->with("UPDATE ".MAIN_DB_PREFIX."ticket SET progress = ".$percent." WHERE rowid = 123");

        $this->assertSame(-1, $ticket->setProgression($percent));
    }

    #[DataProvider('cero_percent_provider')]
    public function test_setProgression_5(int $percent): void
    {
        $ticket =  new Ticket($this->db);

        $this->db->method("query")->willReturn(false);

        $ticket->id = 123;

        $this->db->expects($this->once())
        ->method("query")
        ->with("UPDATE ".MAIN_DB_PREFIX."ticket SET progress = null WHERE rowid = 123");

        $this->assertSame(-1, $ticket->setProgression($percent));
    }
}