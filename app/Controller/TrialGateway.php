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
}