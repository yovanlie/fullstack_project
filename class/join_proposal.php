<?php
require_once("parent.php");

class JoinProposal extends ParentClass
{
    public function createProposal($idmember, $idteam, $description)
    {
        // Check if the member already has a proposal for this team
        $sql = "SELECT COUNT(*) as count FROM join_proposal WHERE idmember = ? AND idteam = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('ii', $idmember, $idteam);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['count'] > 0) {
            return false; // Member already has a proposal for this team
        }

        // Insert the new proposal
        $sql = "INSERT INTO join_proposal (idmember, idteam, description, status) VALUES (?, ?, ?, 'waiting')";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('iis', $idmember, $idteam, $description);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function getProposals($page, $itemsPerPage)
    {
        $offset = ($page - 1) * $itemsPerPage;
        $sql = "SELECT jp.*, m.fname AS member_name, t.name AS team_name 
                FROM join_proposal jp
                JOIN member m ON jp.idmember = m.idmember
                JOIN team t ON jp.idteam = t.idteam
                ORDER BY jp.idjoin_proposal DESC
                LIMIT ? OFFSET ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('ii', $itemsPerPage, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalProposals()
    {
        $sql = "SELECT COUNT(*) as total FROM join_proposal";
        $result = $this->mysqli->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function updateProposalStatus($proposalId, $status)
    {
        $sql = "UPDATE join_proposal SET status = ? WHERE idjoin_proposal = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('si', $status, $proposalId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function getMemberProposal($idmember)
    {
        $sql = "SELECT * FROM join_proposal WHERE idmember = ? AND (status = 'waiting' OR status = 'setuju') LIMIT 1";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $idmember);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function approveProposal($proposalId)
    {
        $this->mysqli->begin_transaction();

        try {
            // Get the proposal details
            $sql = "SELECT idmember, idteam FROM join_proposal WHERE idjoin_proposal = ?";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param('i', $proposalId);
            $stmt->execute();
            $result = $stmt->get_result();
            $proposal = $result->fetch_assoc();

            if (!$proposal) {
                throw new Exception("Proposal not found");
            }

            // Add member to the team
            $sql = "INSERT INTO team_members (idmember, idteam) VALUES (?, ?)";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param('ii', $proposal['idmember'], $proposal['idteam']);
            $stmt->execute();

            // Update proposal status
            $this->updateProposalStatus($proposalId, 'setuju');

            // Delete other proposals for this member
            $sql = "DELETE FROM join_proposal WHERE idmember = ? AND idjoin_proposal != ?";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param('ii', $proposal['idmember'], $proposalId);
            $stmt->execute();

            $this->mysqli->commit();
            return true;
        } catch (Exception $e) {
            $this->mysqli->rollback();
            return false;
        }
    }

    public function rejectProposal($proposalId)
    {
        $sql = "DELETE FROM join_proposal WHERE idjoin_proposal = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $proposalId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function getMemberApprovedProposal($memberId)
    {
        $sql = "SELECT * FROM join_proposal WHERE idmember = ? AND status = 'setuju' LIMIT 1";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $memberId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function isMemberInTeam($memberId)
    {
        $sql = "SELECT COUNT(*) as count FROM team_members WHERE idmember = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $memberId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'] > 0;
    }
}
