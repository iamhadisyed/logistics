#!/usr/bin/env node

/**
 * Quick Verification Script
 * Verifies that duplicate key fixes are in place
 */

const fs = require('fs');
const path = require('path');

const PERMISSIONS_PAGE = path.join(__dirname, '../src/app/[lang]/(dashboard)/users/[id]/permissions/page.tsx');

function verifyPermissionsPage() {
    console.log('🔍 Verifying permissions page fix...\n');
    
    if (!fs.existsSync(PERMISSIONS_PAGE)) {
        console.log('❌ Permissions page not found');
        return false;
    }
    
    const content = fs.readFileSync(PERMISSIONS_PAGE, 'utf8');
    
    // Check 1: Key includes index
    const keyWithIndex = /key=\{`permission-group-\$\{group\.group_id\}-\$\{index\}`\}/;
    if (!keyWithIndex.test(content)) {
        console.log('❌ Key does not include index fallback');
        return false;
    }
    
    // Check 2: Deduplication logic exists
    const hasDedup = /groupMap\.set\(|Array\.from\(groupMap\.values\(\)\)/;
    if (!hasDedup.test(content)) {
        console.log('❌ Deduplication logic missing');
        return false;
    }
    
    // Check 3: Map is used for deduplication
    const hasMap = /new Map\(\)/;
    if (!hasMap.test(content)) {
        console.log('❌ Map-based deduplication missing');
        return false;
    }
    
    console.log('✅ Permissions page: All checks passed');
    return true;
}

// Run verification
const result = verifyPermissionsPage();
process.exit(result ? 0 : 1);
