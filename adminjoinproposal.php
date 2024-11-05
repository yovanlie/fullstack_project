<?php
session_start();
require_once "class/join_proposal.php";

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$joinProposalObj = new JoinProposal();

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$itemsPerPage = 5;

$totalProposals = $joinProposalObj->getTotalProposals();
$totalPages = ceil($totalProposals / $itemsPerPage);

$proposals = $joinProposalObj->getProposals($page, $itemsPerPage);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve'])) {
        $proposalId = $_POST['proposal_id'];
        $result = $joinProposalObj->approveProposal($proposalId);
        
        if ($result) {
            $_SESSION['message'] = "Proposal approved successfully. Other proposals for this member have been removed.";
        } else {
            $_SESSION['error'] = "Failed to approve the proposal.";
        }
    } elseif (isset($_POST['reject'])) {
        $joinProposalObj->rejectProposal($_POST['proposal_id']);
        $_SESSION['message'] = "Proposal rejected successfully.";
    }
    // Refresh the page to show updated statuses
    header("Location: adminjoinproposal.php?page=$page");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <title>Manage Join Proposals</title>
</head>
<body>
    <h1>Manage Join Proposals</h1>
    <?php if (isset($_SESSION['message'])): ?>
        <p style="color: green;"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    <table border="1">
        <tr>
            <th>Member</th>
            <th>Team</th>
            <th>Description</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach ($proposals as $proposal): ?>
            <tr>
                <td><?php echo htmlspecialchars($proposal['member_name']); ?></td>
                <td><?php echo htmlspecialchars($proposal['team_name']); ?></td>
                <td><?php echo htmlspecialchars($proposal['description']); ?></td>
                <td><?php echo htmlspecialchars($proposal['status']); ?></td>
                <td>
                    <?php if ($proposal['status'] === 'waiting'): ?>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="proposal_id" value="<?php echo $proposal['idjoin_proposal']; ?>">
                            <input type="hidden" name="member_id" value="<?php echo $proposal['idmember']; ?>">
                            <button type="submit" name="approve">Approve</button>
                            <button type="submit" name="reject">Reject</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>
