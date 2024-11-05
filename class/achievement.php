<?php
require_once("parent.php");

class Achievement extends ParentClass
{
    /**
     * Display achievements with pagination and optional filtering
     */
    public function displayAchievements($page = 1, $itemsPerPage = 5, $filters = [])
    {
        try {
            $offset = ($page - 1) * $itemsPerPage;

            // Build the base query
            $sql = "SELECT a.*, t.name as team_name 
                    FROM achievement a 
                    LEFT JOIN team t ON a.idteam = t.idteam 
                    WHERE 1=1";

            $whereConditions = [];
            $params = [];
            $types = '';

            // Add filters if they exist
            if (!empty($filters['team_id'])) {
                $whereConditions[] = "a.idteam = ?";
                $params[] = $filters['team_id'];
                $types .= 'i';
            }

            if (!empty($filters['date_from'])) {
                $whereConditions[] = "a.date >= ?";
                $params[] = $filters['date_from'];
                $types .= 's';
            }

            if (!empty($filters['date_to'])) {
                $whereConditions[] = "a.date <= ?";
                $params[] = $filters['date_to'];
                $types .= 's';
            }

            if (!empty($whereConditions)) {
                $sql .= " AND " . implode(" AND ", $whereConditions);
            }

            $sql .= " ORDER BY a.date DESC LIMIT ? OFFSET ?";
            $types .= 'ii';
            $params[] = $itemsPerPage;
            $params[] = $offset;

            $stmt = $this->mysqli->prepare($sql);

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            $output = $this->generateTableHeader();
            $output .= $this->generateTableRows($result);
            $output .= $this->generatePagination($page, $itemsPerPage, $filters);

            return $output;
        } catch (Exception $e) {
            error_log("Error in displayAchievements: " . $e->getMessage());
            return "<p class='error'>An error occurred while displaying achievements.</p>";
        }
    }

    /**
     * Generate table header with conditional admin columns
     */
    private function generateTableHeader()
    {
        $output = "<table class='table table-striped'>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Team</th>";

        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
            $output .= "<th>Actions</th>";
        }

        $output .= "</tr></thead><tbody>";

        return $output;
    }

    /**
     * Generate table rows with data
     */
    private function generateTableRows($result)
    {
        $output = "";
        while ($row = $result->fetch_assoc()) {
            $output .= "<tr>
                            <td>" . htmlspecialchars($row['name']) . "</td>
                            <td>" . htmlspecialchars($row['date']) . "</td>
                            <td>" . htmlspecialchars($row['description']) . "</td>
                            <td>" . htmlspecialchars($row['team_name'] ?? 'No Team') . "</td>";

            if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                $output .= $this->generateAdminActions($row['idachievement']);
            }

            $output .= "</tr>";
        }
        $output .= "</tbody></table>";
        return $output;
    }

    /**
     * Generate admin action buttons
     */
    private function generateAdminActions($achievementId)
    {
        return "<td class='actions'>
                    <a href='editachievement.php?id=" . $achievementId . "' class='btn btn-primary btn-sm'>Edit</a>
                    <button onclick='confirmDelete(" . $achievementId . ")' class='btn btn-danger btn-sm'>Delete</button>
                </td>";
    }

    /**
     * Generate pagination links
     */
    private function generatePagination($currentPage, $itemsPerPage, $filters)
    {
        $totalItems = $this->getTotalAchievements($filters);
        $totalPages = ceil($totalItems / $itemsPerPage);

        $output = "<nav aria-label='Achievement pagination'><ul class='pagination'>";

        // Previous button
        $prevDisabled = $currentPage <= 1 ? ' disabled' : '';
        $output .= "<li class='page-item{$prevDisabled}'>
                        <a class='page-link' href='?page=" . ($currentPage - 1) . $this->getFilterQueryString($filters) . "'>Previous</a>
                    </li>";

        // Page numbers
        for ($i = 1; $i <= $totalPages; $i++) {
            $active = $i === $currentPage ? ' active' : '';
            $output .= "<li class='page-item{$active}'>
                            <a class='page-link' href='?page={$i}" . $this->getFilterQueryString($filters) . "'>{$i}</a>
                        </li>";
        }

        // Next button
        $nextDisabled = $currentPage >= $totalPages ? ' disabled' : '';
        $output .= "<li class='page-item{$nextDisabled}'>
                        <a class='page-link' href='?page=" . ($currentPage + 1) . $this->getFilterQueryString($filters) . "'>Next</a>
                    </li>";

        $output .= "</ul></nav>";
        return $output;
    }

    /**
     * Insert new achievement
     */
    public function insertAchievement($data)
    {
        try {
            $sql = "INSERT INTO achievement (name, date, description, idteam) VALUES (?, ?, ?, ?)";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param('sssi', $data['name'], $data['date'], $data['description'], $data['idteam']);

            if (!$stmt->execute()) {
                throw new Exception("Error inserting achievement: " . $stmt->error);
            }

            return $stmt->insert_id;
        } catch (Exception $e) {
            error_log("Error in insertAchievement: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update existing achievement
     */
    public function updateAchievement($id, $name, $date, $description, $idteam)
    {
        try {
            $sql = "UPDATE achievement SET name = ?, date = ?, description = ?, idteam = ? WHERE idachievement = ?";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param('sssii', $name, $date, $description, $idteam, $id);

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error in updateAchievement: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete achievement
     */
    public function deleteAchievement($id)
    {
        try {
            $sql = "DELETE FROM achievement WHERE idachievement = ?";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param('i', $id);

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error in deleteAchievement: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get single achievement by ID
     */
    public function getAchievementById($id)
    {
        try {
            $sql = "SELECT a.*, t.name as team_name 
                    FROM achievement a 
                    LEFT JOIN team t ON a.idteam = t.idteam 
                    WHERE a.idachievement = ?";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param('i', $id);
            $stmt->execute();

            return $stmt->get_result()->fetch_assoc();
        } catch (Exception $e) {
            error_log("Error in getAchievementById: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get total number of achievements (with filters)
     */
    public function getTotalAchievements($filters = [])
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM achievement WHERE 1=1";
            $params = [];
            $types = '';

            if (!empty($filters['team_id'])) {
                $sql .= " AND idteam = ?";
                $params[] = $filters['team_id'];
                $types .= 'i';
            }

            $stmt = $this->mysqli->prepare($sql);

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            return $row['total'];
        } catch (Exception $e) {
            error_log("Error in getTotalAchievements: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get achievements for a specific team
     */
    public function getTeamAchievements($teamId, $page = 1, $itemsPerPage = 10)
    {
        $offset = ($page - 1) * $itemsPerPage;
        $sql = "SELECT * FROM achievement WHERE idteam = ? ORDER BY date DESC LIMIT ? OFFSET ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('iii', $teamId, $itemsPerPage, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Update team assignment for an achievement
     */
    public function updateTeamAssignment($achievementId, $teamId)
    {
        try {
            $sql = "UPDATE achievement SET idteam = ? WHERE idachievement = ?";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param('ii', $teamId, $achievementId);

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error in updateTeamAssignment: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove team assignment from an achievement
     */
    public function removeTeamAssignment($achievementId)
    {
        return $this->updateTeamAssignment($achievementId, null);
    }

    /**
     * Helper function to generate query string for filters
     */
    private function getFilterQueryString($filters)
    {
        if (empty($filters)) {
            return '';
        }

        $queryParams = [];
        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                $queryParams[] = urlencode($key) . '=' . urlencode($value);
            }
        }

        return '&' . implode('&', $queryParams);
    }

    /**
     * Display status messages
     */
    public function displayMessages()
    {
        $messages = [
            'hasil' => [
                true => "<div class='alert alert-success'>Data achievement berhasil disimpan</div>",
                false => "<div class='alert alert-danger'>Data achievement gagal disimpan</div>"
            ],
            'hapus' => [
                true => "<div class='alert alert-success'>Data berhasil dihapus</div>",
                false => "<div class='alert alert-danger'>Data gagal dihapus</div>"
            ],
            'edit' => [
                true => "<div class='alert alert-success'>Data berhasil diubah</div>",
                false => "<div class='alert alert-danger'>Data gagal diubah</div>"
            ]
        ];

        foreach ($messages as $key => $messageSet) {
            if (isset($_GET[$key])) {
                return $messageSet[$_GET[$key] ? true : false];
            }
        }

        return "";
    }

    public function getAllAchievements()
    {
        $sql = "SELECT * FROM achievement ORDER BY date DESC";
        $result = $this->mysqli->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalTeamAchievements($teamId)
    {
        $sql = "SELECT COUNT(*) as total FROM achievement WHERE idteam = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $teamId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function addAchievementToTeam($teamId, $achievementId)
    {
        try {
            $sql = "UPDATE achievement SET idteam = ? WHERE idachievement = ?";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param('ii', $teamId, $achievementId);
            $stmt->execute();

            return $stmt->affected_rows > 0;
        } catch (Exception $e) {
            error_log("Error in addAchievementToTeam: " . $e->getMessage());
            return false;
        }
    }

    public function changeTeamAchievement($oldTeamId, $newTeamId, $achievementId)
    {
        try {
            $sql = "UPDATE achievement SET idteam = ? WHERE idachievement = ? AND idteam = ?";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param('iii', $newTeamId, $achievementId, $oldTeamId);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                return true;
            } else {
                error_log("No rows affected when changing achievement $achievementId from team $oldTeamId to $newTeamId");
                return false;
            }
        } catch (Exception $e) {
            error_log("Error in changeTeamAchievement: " . $e->getMessage());
            return false;
        }
    }
}
