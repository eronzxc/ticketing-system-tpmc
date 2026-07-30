<?php
/**
 * Department helpers. require_once this alongside db.php in any endpoint
 * that accepts a free-typed department value (e.g. users/create.php,
 * users/update.php).
 *
 * Departments are free text (not a locked dropdown), typed via a
 * <input list="departmentSuggestions"> datalist on the frontend. That
 * flexibility means someone can type "IT department" instead of the
 * canonical "IT Department" — which would silently break the exact-string
 * `department === 'IT Department'` check used everywhere for IT detection
 * (login permissions, requireIT(), notifyIT(), etc.), even though MySQL's
 * default collation makes the UNIQUE constraint on this column
 * case-insensitive (so it won't catch this as a "duplicate" — editing an
 * existing row to a case-variant of its own value doesn't collide with
 * any other row).
 *
 * canonicalizeDepartment() closes that gap: if the typed department
 * matches an existing one case-insensitively, it returns the existing
 * canonical casing instead of the newly-typed variant. If it's a genuinely
 * new department (no case-insensitive match), it's returned as typed.
 */
function canonicalizeDepartment(PDO $pdo, string $department): string {
    $department = trim($department);
    if ($department === '') {
        return $department;
    }

    // Fast path: exact match already exists, nothing to normalize.
    $stmt = $pdo->prepare('SELECT name FROM departments WHERE name = ? LIMIT 1');
    $stmt->execute([$department]);
    $exact = $stmt->fetchColumn();
    if ($exact !== false) {
        return $exact;
    }

    // Case-insensitive fallback: correct casing typos against what already exists.
    $stmt = $pdo->query('SELECT name FROM departments');
    $existing = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($existing as $d) {
        if (strcasecmp($d, $department) === 0) {
            return $d;
        }
    }

    // No match at all — this is a genuinely new department. Register it
    // in the departments table too (not just on this user's row), so it
    // actually shows up in departments/list.php and the account-creation
    // suggestions going forward — otherwise it would only "exist" on this
    // one account and nowhere else in the system.
    $stmt = $pdo->prepare('INSERT IGNORE INTO departments (name) VALUES (?)');
    $stmt->execute([$department]);

    return $department;
}
