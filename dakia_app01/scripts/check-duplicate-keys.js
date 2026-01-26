#!/usr/bin/env node

/**
 * Automated Test: Duplicate Key Detection
 * 
 * This script scans all React components for potential duplicate key issues:
 * 1. Checks for duplicate keys in .map() calls
 * 2. Verifies deduplication logic exists
 * 3. Validates key uniqueness patterns
 * 
 * Run: node scripts/check-duplicate-keys.js
 */

const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const DASHBOARD_DIR = path.join(__dirname, '../src/app/[lang]/(dashboard)');
const VIEWS_DIR = path.join(__dirname, '../src/views');

const errors = [];
const warnings = [];
const passed = [];

// Colors for terminal output
const colors = {
    reset: '\x1b[0m',
    red: '\x1b[31m',
    green: '\x1b[32m',
    yellow: '\x1b[33m',
    blue: '\x1b[34m',
};

function log(message, color = 'reset') {
    console.log(`${colors[color]}${message}${colors.reset}`);
}

function findFiles(dir, fileList = []) {
    const files = fs.readdirSync(dir);
    
    files.forEach(file => {
        const filePath = path.join(dir, file);
        const stat = fs.statSync(filePath);
        
        if (stat.isDirectory()) {
            // Skip node_modules and .next
            if (!file.startsWith('.') && file !== 'node_modules') {
                findFiles(filePath, fileList);
            }
        } else if (file.endsWith('.tsx') || file.endsWith('.ts')) {
            fileList.push(filePath);
        }
    });
    
    return fileList;
}

function checkFile(filePath) {
    const content = fs.readFileSync(filePath, 'utf8');
    const relativePath = path.relative(path.join(__dirname, '..'), filePath);
    const lines = content.split('\n');
    
    // Check 1: Find all .map() calls with keys
    const mapPattern = /\.map\s*\([^)]*\)\s*=>/g;
    const keyPattern = /key\s*=\s*\{[^}]+\}/g;
    
    let hasMap = false;
    let hasKeys = false;
    let hasDeduplication = false;
    let keyIssues = [];
    
    // Check if file has .map() calls
    if (mapPattern.test(content)) {
        hasMap = true;
        
        // Check for key props
        const keyMatches = content.match(keyPattern);
        if (keyMatches) {
            hasKeys = true;
            
            // Check for problematic key patterns
            keyMatches.forEach((keyMatch, index) => {
                // Check for keys without unique prefixes (just {id} or {item.id})
                if (keyMatch.match(/key\s*=\s*\{\s*[a-zA-Z_$][a-zA-Z0-9_$]*\.id\s*\}/)) {
                    const lineNum = content.substring(0, content.indexOf(keyMatch)).split('\n').length;
                    keyIssues.push({
                        line: lineNum,
                        issue: 'Key uses only ID without unique prefix',
                        key: keyMatch
                    });
                }
                
                // Check for keys that might conflict (same pattern in same file)
                const keyValue = keyMatch.match(/\{([^}]+)\}/)?.[1];
                if (keyValue) {
                    const occurrences = (content.match(new RegExp(`key\\s*=\\s*\\{[^}]*${keyValue.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}[^}]*\\}`, 'g')) || []).length;
                    if (occurrences > 1 && !keyValue.includes('index') && !keyValue.includes('${')) {
                        const lineNum = content.substring(0, content.indexOf(keyMatch)).split('\n').length;
                        keyIssues.push({
                            line: lineNum,
                            issue: 'Potential duplicate key pattern found multiple times',
                            key: keyMatch
                        });
                    }
                }
            });
        }
        
        // Check for deduplication logic
        const dedupPatterns = [
            /new Map\(\)/,
            /\.unique\(/,
            /\.filter\([^)]*findIndex/,
            /\.filter\([^)]*indexOf/,
            /Map\(\)/,
            /\.has\(/,
            /\.set\(/,
            /Array\.from\(.*Map/,
        ];
        
        hasDeduplication = dedupPatterns.some(pattern => pattern.test(content));
        
        // Check for state variables that might need deduplication
        const statePattern = /useState<.*\[\]>/g;
        const stateMatches = content.match(statePattern);
        if (stateMatches && hasMap && !hasDeduplication) {
            // Check if data is set directly from API without deduplication
            const setStatePattern = /set\w+\s*\([^)]*response\.data[^)]*\)/g;
            if (setStatePattern.test(content)) {
                keyIssues.push({
                    line: 0,
                    issue: 'Data set from API without deduplication logic',
                    key: 'Missing deduplication'
                });
            }
        }
    }
    
    return {
        file: relativePath,
        hasMap,
        hasKeys,
        hasDeduplication,
        keyIssues
    };
}

function checkBackendDeduplication() {
    const backendDir = path.join(__dirname, '../../dakia_backend01/app/Http/Controllers/Api');
    if (!fs.existsSync(backendDir)) {
        warnings.push('Backend directory not found, skipping backend checks');
        return;
    }
    
    const controllerFiles = findFiles(backendDir);
    controllerFiles.forEach(filePath => {
        const content = fs.readFileSync(filePath, 'utf8');
        const relativePath = path.relative(path.join(__dirname, '../..'), filePath);
        
        // Check for methods that return lists
        if (content.includes('->get()') || content.includes('->paginate(')) {
            // Check if unique() is used
            if (!content.includes('->unique(') && !content.includes('->distinct(')) {
                warnings.push(`Backend: ${relativePath} - Returns list without unique() or distinct()`);
            }
        }
    });
}

function runTests() {
    log('\n🔍 Starting Duplicate Key Detection Tests...\n', 'blue');
    
    // Find all component files
    const files = [];
    if (fs.existsSync(DASHBOARD_DIR)) {
        files.push(...findFiles(DASHBOARD_DIR));
    }
    if (fs.existsSync(VIEWS_DIR)) {
        files.push(...findFiles(VIEWS_DIR));
    }
    
    log(`Found ${files.length} component files to check\n`, 'blue');
    
    // Check each file
    files.forEach(filePath => {
        const result = checkFile(filePath);
        
        if (result.hasMap && result.hasKeys) {
            if (result.keyIssues.length > 0) {
                errors.push({
                    file: result.file,
                    issues: result.keyIssues
                });
            } else if (!result.hasDeduplication) {
                warnings.push({
                    file: result.file,
                    issue: 'Has .map() with keys but no deduplication logic detected'
                });
            } else {
                passed.push(result.file);
            }
        }
    });
    
    // Check backend
    checkBackendDeduplication();
    
    // Print results
    log('\n📊 Test Results:\n', 'blue');
    
    if (errors.length > 0) {
        log(`❌ ERRORS: ${errors.length}`, 'red');
        errors.forEach(({ file, issues }) => {
            log(`\n  ${file}:`, 'red');
            issues.forEach(issue => {
                log(`    Line ${issue.line}: ${issue.issue}`, 'red');
                log(`    Key: ${issue.key}`, 'yellow');
            });
        });
    }
    
    if (warnings.length > 0) {
        log(`\n⚠️  WARNINGS: ${warnings.length}`, 'yellow');
        warnings.forEach(warning => {
            if (typeof warning === 'string') {
                log(`  ${warning}`, 'yellow');
            } else {
                log(`  ${warning.file}: ${warning.issue}`, 'yellow');
            }
        });
    }
    
    if (passed.length > 0) {
        log(`\n✅ PASSED: ${passed.length} files`, 'green');
    }
    
    // Summary
    log('\n' + '='.repeat(60), 'blue');
    log(`Total Files Checked: ${files.length}`, 'blue');
    log(`Errors: ${errors.length}`, errors.length > 0 ? 'red' : 'green');
    log(`Warnings: ${warnings.length}`, warnings.length > 0 ? 'yellow' : 'green');
    log(`Passed: ${passed.length}`, 'green');
    log('='.repeat(60) + '\n', 'blue');
    
    // Exit with error code if issues found
    if (errors.length > 0) {
        log('❌ Tests FAILED - Please fix the errors above', 'red');
        process.exit(1);
    } else if (warnings.length > 0) {
        log('⚠️  Tests PASSED with warnings', 'yellow');
        process.exit(0);
    } else {
        log('✅ All tests PASSED', 'green');
        process.exit(0);
    }
}

// Run tests
runTests();
