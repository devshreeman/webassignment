<?php
// connect to database
include('../config/db.php');

// make sure session is started and staff is logged in
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit;
}

// grab staff ID and filter options
$staffId = $_SESSION['staff']['StaffID'];
$filterProgramme = intval($_GET['prog'] ?? 0);
$export = $_GET['export'] ?? '';

try {
    // build query to get interested students for this staff member's modules
    $programmeFilter = '';
    $params = [$staffId];
    
    // add programme filter if selected
    if ($filterProgramme > 0) {
        $programmeFilter = ' AND p.ProgrammeID = ?';
        $params[] = $filterProgramme;
    }
    
    // get all interested students for programmes containing this staff's modules
    $stmt = $pdo->prepare("
        SELECT 
            i.InterestID,
            COALESCE(s.FullName, i.StudentName) as StudentName,
            COALESCE(s.Email, i.Email) as Email,
            p.ProgrammeName, 
            i.RegisteredAt
        FROM interestedstudents i
        JOIN programmes p ON i.ProgrammeID = p.ProgrammeID
        LEFT JOIN students s ON i.StudentID = s.StudentID
        WHERE EXISTS (
            SELECT 1
            FROM programmemodules pm
            JOIN modules m ON pm.ModuleID = m.ModuleID
            WHERE pm.ProgrammeID = i.ProgrammeID
            AND m.ModuleLeaderID = ?
        )" . $programmeFilter . "
        GROUP BY i.InterestID
        ORDER BY i.RegisteredAt DESC
    ");
    $stmt->execute($params);
    $interestedStudents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // handle CSV export if requested
    if ($export === '1') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="interested_students_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // add UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // write CSV headers
        fputcsv($output, ['Name', 'Email', 'Programme', 'Registration Date']);
        
        // write student data
        foreach ($interestedStudents as $student) {
            fputcsv($output, [
                $student['StudentName'],
                $student['Email'],
                $student['ProgrammeName'],
                date('Y-m-d', strtotime($student['RegisteredAt']))
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    // get list of programmes for filter dropdown
    $stmtProgrammes = $pdo->prepare("
        SELECT DISTINCT p.ProgrammeID, p.ProgrammeName
        FROM programmes p
        JOIN programmemodules pm ON p.ProgrammeID = pm.ProgrammeID
        JOIN modules m ON pm.ModuleID = m.ModuleID
        WHERE m.ModuleLeaderID = ?
        ORDER BY p.ProgrammeName
    ");
    $stmtProgrammes->execute([$staffId]);
    $programmes = $stmtProgrammes->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $interestedStudents = [];
    $programmes = [];
}

$pageTitle = 'Interested Students';
$activePage = 'interested-students';
$cssBase = '../';
$rootBase = '../';

include('../includes/header.php');
?>

<section style="background: var(--color-surface); border-bottom: 1px solid var(--color-border); padding: var(--space-8) 0;">
  <div class="container">
    <div class="admin-topbar" style="margin-bottom: 0;">
      <div>
        <h1 class="admin-page-title" style="margin-bottom: var(--space-1);">Interested Students</h1>
        <p class="admin-page-subtitle">Students who have registered interest in programmes containing your modules.</p>
      </div>
      <div>
        <span class="status-badge" style="background:var(--color-primary); color:#fff; font-size:var(--text-lg); padding:var(--space-2) var(--space-4);">
          <?php echo count($interestedStudents); ?> Total
        </span>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-6); flex-wrap:wrap; gap:var(--space-4);">
      <form method="GET" action="interested_students.php" style="display:flex; gap:var(--space-3); align-items:center;">
        <label for="prog" class="form-label" style="margin:0;">Filter by Programme:</label>
        <select name="prog" id="prog" class="form-input" style="width:auto; min-width:250px;" onchange="this.form.submit()">
          <option value="0">All Programmes</option>
          <?php foreach ($programmes as $prog): ?>
            <option value="<?php echo $prog['ProgrammeID']; ?>" 
                    <?php echo ($filterProgramme === $prog['ProgrammeID']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($prog['ProgrammeName']); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if ($filterProgramme > 0): ?>
          <a href="interested_students.php" class="btn btn-secondary">Clear Filter</a>
        <?php endif; ?>
      </form>
      
      <?php if (!empty($interestedStudents)): ?>
        <a href="interested_students.php?export=1<?php echo $filterProgramme > 0 ? '&prog=' . $filterProgramme : ''; ?>" 
           class="btn btn-primary">
          Export to CSV
        </a>
      <?php endif; ?>
    </div>
    
    <?php if (empty($interestedStudents)): ?>
      <div class="empty-state" style="padding:var(--space-12); border:1px solid var(--color-border); border-radius:var(--radius-lg);">
        <div class="empty-state__icon" aria-hidden="true">👥</div>
        <h3 class="empty-state__title">No interested students</h3>
        <p>No students have registered interest in programmes containing your modules yet.</p>
      </div>
    <?php else: ?>
      <div class="data-table-wrapper">
        <table class="data-table" aria-label="Interested Students">
          <thead>
            <tr>
              <th scope="col">Name</th>
              <th scope="col">Email</th>
              <th scope="col">Programme</th>
              <th scope="col">Registration Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($interestedStudents as $student): ?>
              <tr>
                <td><?php echo htmlspecialchars($student['StudentName']); ?></td>
                <td><?php echo htmlspecialchars($student['Email']); ?></td>
                <td><?php echo htmlspecialchars($student['ProgrammeName']); ?></td>
                <td><?php echo date('M d, Y', strtotime($student['RegisteredAt'])); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
    
    <div style="margin-top: var(--space-8);">
      <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>
  </div>
</section>

<?php include('../includes/footer.php'); ?>
