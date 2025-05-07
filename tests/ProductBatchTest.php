<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversMethod(ProductBatch::class, "delete")]
final class ProductBatchTest extends TestCase
{
    protected function setUp(): void
    {
        // Incluyendo archivos y configuraciones necesarias para la base de datos
        global $DOL_DOCUMENT_ROOT;
        define("LOG_DEBUG", 7);
        define("LOG_ERROR", 3);
        $DOL_DOCUMENT_ROOT = define("DOL_DOCUMENT_ROOT", dirname(__FILE__) . "/../htdocs");
        // Funciones que se utilizan en la clase
        require_once dirname(__FILE__) . '/../htdocs/core/lib/functions.lib.php';
        require_once dirname(__FILE__) . '/../htdocs/core/db/mysqli.class.php';
        // Incluyendo la clase
        require_once dirname(__FILE__) . '/../htdocs/product/class/productbatch.class.php';
        require_once dirname(__FILE__) . '/../htdocs/user/class/user.class.php';

        $this->db = $this->createMock(DoliDBMysqli::class);
        $this->db->method("lasterror")->willReturn("Error");
        $this->db->method("prefix")->willReturn("llx_doli_product_batch_");

        $this->user = $this->getMockBuilder(User::class)
        ->setConstructorArgs([$this->db])
        ->getMock();
    }

    public static function no_trigger_provider() {
        return [[0,], ];
    }

    #[DataProvider('no_trigger_provider')]
    public function test_delete_1(int $notrigger): void
    {
        $product_batch =  new Productbatch($this->db);

        $this->db->method("query")->willReturn(true);

        $product_batch->id = 123;
        $product_batch->errors = [];

        $this->db->expects($this->once())
        ->method('prefix');

        $this->db->expects($this->once())
        ->method('query');

        $this->db->expects($this->once())
        ->method("begin");

        $this->db->expects($this->once())
        ->method('commit');

        $this->db->expects($this->never())
        ->method('rollback');

        $this->assertSame(1, $product_batch->delete($this->user, $notrigger));
    }

    #[DataProvider('no_trigger_provider')]
    public function test_delete_2(int $notrigger): void
    {
        $product_batch =  new Productbatch($this->db);

        $this->db->method("query")->willReturn(false);

        $product_batch->id = 123;
        $product_batch->errors = [];

        $this->db->expects($this->once())
        ->method('prefix');

        $this->db->expects($this->once())
        ->method('query');

        $this->db->expects($this->once())
        ->method("begin");

        $this->db->expects($this->never())
        ->method('commit');

        $this->db->expects($this->once())
        ->method('rollback');

        $this->assertSame(-1, $product_batch->delete($this->user, $notrigger));
        $this->assertSame("Error Error", $product_batch->errors[0]);
        $this->assertSame("Error Error", $product_batch->error);
    }

    #[DataProvider('no_trigger_provider')]
    public function test_delete_3(int $notrigger): void
    {
        $product_batch =  new Productbatch($this->db);

        $this->db->method("query")->willReturn(false);

        $product_batch->id = 123;
        $product_batch->errors = [];

        $this->db->expects($this->never())
        ->method('prefix');

        $this->db->expects($this->never())
        ->method('query');

        $this->db->expects($this->once())
        ->method("begin");

        $this->db->expects($this->never())
        ->method('commit');

        $this->db->expects($this->once())
        ->method('rollback');

        $this->assertSame(-1, $product_batch->delete($this->user, $notrigger));
    }
}