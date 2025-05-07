<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversMethod(UserGroup::class, "fetch")]
final class UserGroupTest extends TestCase
{
    protected function setUp(): void
    {
        // Incluyendo archivos y configuraciones necesarias para la base de datos
        global $DOL_DOCUMENT_ROOT;
        $DOL_DOCUMENT_ROOT = define("DOL_DOCUMENT_ROOT", dirname(__FILE__) . "/../htdocs");
        // Funciones que se utilizan en la clase
        require_once dirname(__FILE__) . '/../htdocs/core/lib/functions.lib.php';
        require_once dirname(__FILE__) . '/../htdocs/core/db/mysqli.class.php';
        // Incluyendo la clase
        require_once dirname(__FILE__) . '/../htdocs/user/class/usergroup.class.php';

        $this->db = $this->createMock(DoliDBMysqli::class);
        $this->db->method("escape")->willReturn("Texto escapado");
        $this->db->method("lasterror")->willReturn("Error");
    }

    public static function no_load_provider() {
        return [[0, 'NombreDelGrupo', false],];
    }

    public static function no_group_provider() {
        return [[123, "", true],];
    }

    public static function group_load_provider() {
        return [[1234, "GrupoInexistente", true],];
    }

   #[DataProvider('no_load_provider')]
    public function test_fetch_1(int $id, string $group_name, bool $load_members): void
    {
        $user_group =  $this->getMockBuilder(UserGroup::class)
        ->onlyMethods(['listUsersForGroup','fetchCommon'])
        ->setConstructorArgs([$this->db])
        ->getMock();

        $user_group->method('fetchCommon')->willReturn(1);

        $user_group->expects($this->never())
        ->method('listUsersForGroup');

        $this->db->expects($this->once())
        ->method('escape')
        ->with($group_name);

        $this->db->expects($this->never())
        ->method('lasterror');

        $this->assertSame(1, $user_group->fetch($id, $group_name, $load_members));
        $this->assertEmpty($user_group->error);
    }

    #[DataProvider('no_group_provider')]
    public function test_fetch_2(int $id, string $group_name, bool $load_members): void
    {
        $user_group =  $this->getMockBuilder(UserGroup::class)
        ->onlyMethods(['listUsersForGroup','fetchCommon'])
        ->setConstructorArgs([$this->db])
        ->getMock();

        $user_group->method("fetchCommon")->willReturn(1);
        $user_group->method("listUsersForGroup")->willReturn([1,2,3,4,5,6]);
        $user_group->expects($this->once())
        ->method("fetchCommon");

        $user_group->nom = "Nombre grupo";

        $user_group->expects($this->once())
        ->method('listUsersForGroup');

        $this->db->expects($this->never())
        ->method('escape');

        $this->db->expects($this->never())
        ->method('lasterror');

        $this->assertSame(1, $user_group->fetch($id, $group_name, $load_members));
        $this->assertSame("Nombre grupo", $user_group->name);
        $this->assertSame([1,2,3,4,5,6], $user_group->members);
        $this->assertEmpty($user_group->error);
    }

    #[DataProvider('no_group_provider')]
    public function test_fetch_3(int $id, string $group_name, bool $load_members): void
    {
        $user_group =  $this->getMockBuilder(UserGroup::class)
        ->onlyMethods(['listUsersForGroup','fetchCommon'])
        ->setConstructorArgs([$this->db])
        ->getMock();

        $user_group->method("fetchCommon")->willReturn(0);

        $user_group->expects($this->once())
        ->method("fetchCommon")
        ->with($id);

        $user_group->nom = "Nombre grupo";

        $user_group->expects($this->never())
        ->method('listUsersForGroup');

        $this->db->expects($this->never())
        ->method('escape');

        $this->db->expects($this->once())
        ->method('lasterror');

        $this->assertSame(-1, $user_group->fetch($id, $group_name, $load_members));
        $this->assertSame("Nombre grupo", $user_group->name);
        $this->assertEmpty($user_group->members);
        $this->assertNotEmpty($user_group->error);
    }

    #[DataProvider('group_load_provider')]
    public function test_fetch_4(int $id, string $group_name, bool $load_members): void
    {
        $user_group =  $this->getMockBuilder(UserGroup::class)
        ->onlyMethods(['listUsersForGroup','fetchCommon'])
        ->setConstructorArgs([$this->db])
        ->getMock();

        $user_group->method("fetchCommon")->willReturn(0);

        $user_group->expects($this->once())
        ->method("fetchCommon")
        ->with(0, '', ' AND nom = \''.$this->db->escape($group_name).'\'');

        $user_group->nom = "Nombre grupo";

        $user_group->expects($this->never())
        ->method('listUsersForGroup');

        $this->db->expects($this->once())
        ->method('escape');

        $this->db->expects($this->once())
        ->method('lasterror');

        $this->assertSame(-1, $user_group->fetch($id, $group_name, $load_members));
        $this->assertSame("Nombre grupo", $user_group->name);
        $this->assertEmpty($user_group->members);
        $this->assertNotEmpty($user_group->error);
    }
}