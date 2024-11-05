<?php
require_once("parent.php");

class Event extends ParentClass
{
    public function displayEvents($page = 1, $itemsPerPage = 5)
    {
        $offset = ($page - 1) * $itemsPerPage;
        $sql = "SELECT * FROM event ORDER BY date DESC LIMIT ? OFFSET ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('ii', $itemsPerPage, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        $output = "<table border='1'>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Description</th>";

        if ($_SESSION['user_role'] === 'admin') {
            $output .= "<th>Actions</th>";
        }

        $output .= "</tr>";

        while ($row = $result->fetch_assoc()) {
            $output .= "<tr>
                            <td>{$row['idevent']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['date']}</td>
                            <td>{$row['description']}</td>";

            if ($_SESSION['user_role'] === 'admin') {
                $output .= "<td>
                                <a href='editevent.php?id={$row['idevent']}'>Edit</a> |
                                <a href='prosesdeleteevent.php?idhapus={$row['idevent']}'>Delete</a>
                            </td>";
            }

            $output .= "</tr>";
        }

        $output .= "</table>";

        // Add pagination links
        $totalEvents = $this->getTotalEvents();
        $totalPages = ceil($totalEvents / $itemsPerPage);
        $output .= "<div class='pagination'>";
        for ($i = 1; $i <= $totalPages; $i++) {
            $output .= "<a href='?page=$i'>$i</a> ";
        }
        $output .= "</div>";

        return $output;
    }

    public function displayMessages()
    {
        if (isset($_GET['hasil'])) {
            if ($_GET['hasil']) {
                return "<p>Data event berhasil disimpan</p>";
            } else {
                return "<p>Data event gagal disimpan</p>";
            }
        }

        if (isset($_GET['hapus'])) {
            if ($_GET['hapus']) {
                return "<p>Data berhasil dihapus</p>";
            } else {
                return "<p>Data gagal dihapus</p>";
            }
        }

        if (isset($_GET['edit'])) {
            if ($_GET['edit']) {
                return "<p>Data berhasil diubah</p>";
            } else {
                return "<p>Data gagal diubah</p>";
            }
        }

        return ""; // No messages to display
    }

    public function getTeamEvents($teamId, $page = 1, $itemsPerPage = 10)
    {
        $offset = ($page - 1) * $itemsPerPage;
        $sql = "SELECT e.* 
                FROM event e 
                JOIN event_teams et ON e.idevent = et.idevent 
                WHERE et.idteam = ? 
                ORDER BY e.date DESC 
                LIMIT ? OFFSET ?";
                
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('iii', $teamId, $itemsPerPage, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalTeamEvents($teamId)
    {
        $sql = "SELECT COUNT(*) as total 
                FROM event_teams 
                WHERE idteam = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $teamId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function addTeamEvent($teamId, $eventId)
    {
        // Check if the relation already exists
        if (!$this->isTeamEventExists($teamId, $eventId)) {
            $sql = "INSERT INTO event_teams (idteam, idevent) VALUES (?, ?)";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param('ii', $teamId, $eventId);
            $stmt->execute();
            return $stmt->affected_rows > 0;
        }
        return false;
    }

    public function removeTeamEvent($teamId, $eventId)
    {
        $sql = "DELETE FROM event_teams WHERE idteam = ? AND idevent = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('ii', $teamId, $eventId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function isTeamEventExists($teamId, $eventId)
    {
        $sql = "SELECT 1 FROM event_teams WHERE idteam = ? AND idevent = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('ii', $teamId, $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function getAllEvents()
    {
        $sql = "SELECT * FROM event ORDER BY date DESC";
        $result = $this->mysqli->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getEventById($id)
    {
        $sql = "SELECT * FROM event WHERE idevent = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function createEvent($name, $date, $description, $teamId)
    {
        $this->mysqli->begin_transaction();

        try {
            $sql = "INSERT INTO event (name, date, description) VALUES (?, ?, ?)";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param('sss', $name, $date, $description);
            $stmt->execute();
            $eventId = $this->mysqli->insert_id;

            if ($teamId) {
                $sql = "INSERT INTO event_teams (idevent, idteam) VALUES (?, ?)";
                $stmt = $this->mysqli->prepare($sql);
                $stmt->bind_param('ii', $eventId, $teamId);
                $stmt->execute();
            }

            $this->mysqli->commit();
            return true;
        } catch (Exception $e) {
            $this->mysqli->rollback();
            return false;
        }
    }

    public function updateEvent($id, $name, $date, $description, $teamId)
    {
        $this->mysqli->begin_transaction();

        try {
            $sql = "UPDATE event SET name = ?, date = ?, description = ? WHERE idevent = ?";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param('sssi', $name, $date, $description, $id);
            $stmt->execute();

            // Delete existing team association
            $sql = "DELETE FROM event_teams WHERE idevent = ?";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param('i', $id);
            $stmt->execute();

            // Insert new team association
            if ($teamId) {
                $sql = "INSERT INTO event_teams (idevent, idteam) VALUES (?, ?)";
                $stmt = $this->mysqli->prepare($sql);
                $stmt->bind_param('ii', $id, $teamId);
                $stmt->execute();
            }

            $this->mysqli->commit();
            return true;
        } catch (Exception $e) {
            $this->mysqli->rollback();
            return false;
        }
    }

    public function deleteEvent($id)
    {
        $this->mysqli->begin_transaction();

        try {
            // Delete team associations
            $sql = "DELETE FROM event_teams WHERE idevent = ?";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param('i', $id);
            $stmt->execute();

            // Delete the event
            $sql = "DELETE FROM event WHERE idevent = ?";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param('i', $id);
            $stmt->execute();

            $this->mysqli->commit();
            return true;
        } catch (Exception $e) {
            $this->mysqli->rollback();
            return false;
        }
    }

    public function getTeamsInEvent($eventId)
    {
        $sql = "SELECT t.* 
                FROM team t 
                JOIN event_teams et ON t.idteam = et.idteam 
                WHERE et.idevent = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalEvents()
    {
        $sql = "SELECT COUNT(*) as total FROM event";
        $result = $this->mysqli->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function getTeamInEvent($eventId)
    {
        $sql = "SELECT t.* FROM team t
                JOIN event_teams et ON t.idteam = et.idteam
                WHERE et.idevent = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
?>