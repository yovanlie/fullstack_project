<?php
session_start();
require_once "class/team.php";
require_once "class/join_proposal.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$teamObj = new Team();
$joinProposalObj = new JoinProposal();

// Check if the member is already in a team
if ($joinProposalObj->isMemberInTeam($_SESSION['user_id'])) {
    $_SESSION['error'] = "You are already a member of a team and cannot join another team.";
    header("Location: dashboard.php");
    exit();
}

$teams = $teamObj->getAllTeams();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teamId = $_POST['team_id'];
    $description = $_POST['description'];

    $result = $joinProposalObj->createProposal($_SESSION['user_id'], $teamId, $description);

    if ($result) {
        $_SESSION['message'] = "Join proposal submitted successfully!";
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Failed to submit join proposal. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <title>Join Team</title>
</head>

<body>
    <h1>Join Team</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="post">
        <p>
            <label for="team_id">Select Team</label>
            <select name="team_id" id="team_id" required>
                <option value="">Choose a team...</option>
                <?php foreach ($teams as $team): ?>
                    <option value="<?php echo $team['idteam']; ?>"><?php echo htmlspecialchars($team['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="description">Description</label>
            <textarea name="description" id="description" required></textarea>
        </p>
        <button type="submit">Submit Join Proposal</button>
    </form>
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>

</html>