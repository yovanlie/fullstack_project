<?php
require_once "parent.php";

class Game extends ParentClass
{
    public function displayGames($page = 1, $itemsPerPage = 5)
    {
        $offset = ($page - 1) * $itemsPerPage;
        $sql = "SELECT * FROM game LIMIT ? OFFSET ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('ii', $itemsPerPage, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $output = "<table border='1'>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>";
        
        while ($row = $result->fetch_assoc()) {
            $output .= "<tr>
                            <td>{$row['idgame']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['description']}</td>
                            <td>
                                <a href='editgame.php?id={$row['idgame']}'>Edit</a> |
                                <a href='deletegame.php?id={$row['idgame']}'>Delete</a>
                            </td>
                        </tr>";
        }
        
        $output .= "</table>";

        // Add pagination links
        $totalGames = $this->getTotalGames();
        $totalPages = ceil($totalGames / $itemsPerPage);
        $output .= "<div class='pagination'>";
        for ($i = 1; $i <= $totalPages; $i++) {
            $activeClass = ($i == $page) ? 'class="active"' : '';
            $output .= "<a href='?page=$i' $activeClass>$i</a> ";
        }
        $output .= "</div>";

        return $output;
    }

    public function getTotalGames()
    {
        $sql = "SELECT COUNT(*) as total FROM game";
        $result = $this->mysqli->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function displayMessages()
    {
        if (isset($_GET['hasil'])) {
            if ($_GET['hasil']) {
                return "<p>Data game berhasil disimpan</p>";
            } else {
                return "<p>Data game gagal disimpan</p>";
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
    public function insertGame($name, $description)
    {
        $sql = "INSERT INTO game (name, description) VALUES (?, ?)";
        $stmt = $this->mysqli->prepare($sql);

        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($this->mysqli->error));
        }

        $stmt->bind_param('ss', $name, $description);
        $stmt->execute();

        $affectedRows = $stmt->affected_rows;
        $last_id = $stmt->insert_id;

        $stmt->close();

        return $affectedRows; // Return the number of affected rows
    }
    public function updateGame($idgame, $name, $description)
    {
        $sql = "UPDATE game SET name = ?, description = ? WHERE idgame = ?";
        $stmt = $this->mysqli->prepare($sql);

        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($this->mysqli->error));
        }

        $stmt->bind_param('ssi', $name, $description, $idgame);
        $stmt->execute();

        $affectedRows = $stmt->affected_rows;
        $stmt->close();

        return $affectedRows; // Return the number of affected rows
    }
    public function getGameById($idgame)
    {
        $sql = "SELECT * FROM game WHERE idgame = ?";
        $stmt = $this->mysqli->prepare($sql);

        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($this->mysqli->error));
        }

        $stmt->bind_param('i', $idgame);
        $stmt->execute();
        $res = $stmt->get_result();
        $game = $res->fetch_assoc();

        $stmt->close();
        return $game; // Return the game details
    }
    public function deleteGame($idgame)
    {
        $sql = "DELETE FROM game WHERE idgame = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $idgame);
        $stmt->execute();

        $affectedRows = $stmt->affected_rows;
        $stmt->close();

        return $affectedRows;
    }

    public function getGames($page, $itemsPerPage = 10)
    {
        $offset = ($page - 1) * $itemsPerPage;
        $sql = "SELECT * FROM game ORDER BY name LIMIT ? OFFSET ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('ii', $itemsPerPage, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
