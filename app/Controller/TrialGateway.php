<?php

class TrialGateway
{
    private PDO $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM trial";

        $result = $this->conn->query($sql);

        $data = [];

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

            $row["is_active"] = (bool) $row["is_active"];

            $data[] = $row;
        }

        return $data;
    }

    public function create(array $data): string
    {
        $sql = "INSERT INTO trial (name, email, is_active)
                VALUES (:name, :email, :is_active)";

        $result = $this->conn->prepare($sql);

        $result->bindValue(":name", $data["name"], PDO::PARAM_STR);
        $result->bindValue(":email", $data["email"], PDO::PARAM_STR);
        $result->bindValue(":is_active", (bool) $data["is_active"] ?? true, PDO::PARAM_BOOL);

        $result->execute();

        return $this->conn->lastInsertId();
    }

    public function get(string $id): array | false
    {
        $sql = "SELECT * FROM trial WHERE id = :id";

        $result = $this->conn->prepare($sql);

        $result->bindValue(":id", $id, PDO::PARAM_INT);

        $result->execute();

        $data = $result->fetch(PDO::FETCH_ASSOC);

        if ($data !== false) {
            $data["is_active"] = (bool) $data["is_active"];
        }

        return $data;
    }

    public function update(array $current, array $new): int
    {
        $sql = "UPDATE trial SET full_name = :full_name, email = :email, is_active = :is_active WHERE id = :id";

        $result = $this->conn->prepare($sql);

        $result->bindValue(":full_name", $new["full_name"] ?? $current["full_name"], PDO::PARAM_STR);
        $result->bindValue(":email", $new["email"] ?? $current["email"], PDO::PARAM_STR);
        $result->bindValue(":is_active", $new["is_active"] ?? $current["is_active"], PDO::PARAM_BOOL);
        $result->bindValue(":id", $current["id"], PDO::PARAM_INT);

        $result->execute();

        return $result->rowCount();
    }

    public function delete(string $id): int
    {
        $sql = "DELETE FROM trial WHERE id = :id";

        $result = $this->conn->prepare($sql);

        $result->bindValue(":id", $id);

        return $result->rowCount();
    }
}
