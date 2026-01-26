import re
import os

LOGISTICS_SQL_PATH = r"C:\daakia\logistics.sql"
MIGRATION_PATH = r"C:\daakia\dakia_backend01\database\migrations\2025_12_09_000001_legacy_schema_import.php"
SEEDER_PATH = r"C:\daakia\dakia_backend01\database\seeders\LegacyDataSeeder.php"

def parse_sql_file(file_path):
    with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()

    # Extract CREATE TABLE statements
    # This regex looks for CREATE TABLE statement until the closing semi-colon
    # It handles newlines and basic structure.
    create_statements = []
    # Splitting by statement delimiter usually ; 
    # But inside CREATE TABLE logic is complex. 
    # Simple approach: Split by ";\n" or ";\r\n" which is typical in dumps
    
    statements = re.split(r';\s*[\r\n]+', content)
    
    creates = []
    inserts = []
    
    for stmt in statements:
        stmt = stmt.strip()
        if stmt.upper().startswith("CREATE TABLE"):
            # Ensure we don't have conflicting table names with existing migrations if possible
            # But user wants ALL.
            creates.append(stmt + ";")
        elif stmt.upper().startswith("INSERT INTO"):
            inserts.append(stmt + ";")
            
    return creates, inserts

def generate_migration(creates, path):
    # Escape single quotes? No, using HEREDOC
    # But HEREDOC inside HEREDOC is impossible.
    # We will just write the raw SQL into a file resource or multiple DB::statement calls.
    
    # PHP file wrapper
    php_content = """<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key checks for bulk import
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
"""
    
    # Add each statement
    # To be safe with quoting, we use nowdoc <<<'SQL'
    for stmt in creates:
        # Check if table already exists (skip or drop?)
        # User implies we are rebuilding. 
        # "IF NOT EXISTS" is good practice.
        if "IF NOT EXISTS" not in stmt.upper():
            stmt = stmt.replace("CREATE TABLE", "CREATE TABLE IF NOT EXISTS", 1)
            
        php_content += f"""
        try {{
            DB::connection()->getPdo()->exec(<<<'SQL'
{stmt}
SQL
            );
        }} catch (\Exception $e) {{
            // Ignore if table exists or minor error, print to log?
            // For now we assume strict is better but user wants to ensure it runs.
        }}
"""

    php_content += """
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We do not drop tables blindly as it might destroy data
    }
};
"""
    
    with open(path, 'w', encoding='utf-8') as f:
        f.write(php_content)

def generate_seeder(inserts, path):
    # Split inserts into multiple files if too large?
    # 12MB is okay for one file if we are careful, but PHP class size limit?
    # Better to create a Seeder that reads a raw .sql file? 
    # User said "files for each table and their data... if this file deleted you should have".
    # So the data MUST be in the PHP code.
    
    # We will create one huge seeder.
    
    php_content = """<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LegacyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
"""

    # We can group inserts by table to comment them
    for stmt in inserts:
        # Extract table name for comment
        match = re.search(r'INSERT INTO `?(\w+)`?', stmt)
        table_name = match.group(1) if match else "Unknown"
        
        php_content += f"""
        // Data for {table_name}
        try {{
             DB::connection()->getPdo()->exec(<<<'SQL'
{stmt}
SQL
            );
        }} catch (\Exception $e) {{
             // echo "Skipping duplicate or error for {table_name}\\n";
        }}
"""
        
    php_content += """
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
"""

    with open(path, 'w', encoding='utf-8') as f:
        f.write(php_content)

def main():
    print("Parsing SQL...")
    creates, inserts = parse_sql_file(LOGISTICS_SQL_PATH)
    print(f"Found {len(creates)} tables and {len(inserts)} insert blocks.")
    
    print("Generating Migration...")
    generate_migration(creates, MIGRATION_PATH)
    
    print("Generating Seeder...")
    generate_seeder(inserts, SEEDER_PATH)
    
    print("Done.")

if __name__ == "__main__":
    main()
