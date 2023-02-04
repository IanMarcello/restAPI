<?php

class TrialController
{
    public function __construct(private TrialGateway $gateway)
    {
    }

    public function processRequest(string $method, ?string $id): void
    {

        if ($id) {
            $this->processResourceRequest($method, $id);
        } else {
            $this->processCollectionRequest($method);
        }
    }

    private function processResourceRequest(string $method, string $id): void
    {
        $trial = $this->gateway->get($id);

        if (!$trial) {
            http_response_code(404);
            echo json_encode([
                "message" => "Fetch trial failed."
            ]);
            return;
        }

        switch ($method) {
            case "GET":
                echo json_encode($trial);
                break;

            case "PATCH":
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->getValidationErrors($data, false);

                if (!empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }

                $rows = $this->gateway->update($trial, $data);

                http_response_code(201);
                echo json_encode([
                    "message" => "Update trial for $id is successful",
                    "rows" => $rows
                ]);
                break;

            case "DELETE":
                $rows = $this->gateway->delete($id);

                echo json_encode([
                    "message" => "Delete trial for $id is successful",
                    "rows" => $rows
                ]);
                break;

            default:
                http_response_code(405);
                header("Allow: GET,PATCH, DELETE");
                break;
        }
    }

    private function processCollectionRequest(string $method)
    {
        switch ($method) {
            case "GET":
                echo json_encode($this->gateway->getAll(), JSON_PRETTY_PRINT);
                break;

            case "POST":
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->getValidationErrors($data);

                if (!empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }

                $id = $this->gateway->create($data);

                http_response_code(201);
                echo json_encode([
                    "message" => "Trial successful",
                    "id" => $id
                ]);
                break;

            default:
                http_response_code(405);
                header("Allow: GET, POST");
                break;
        }
    }

    private function getValidationErrors(array $data, bool $is_new = true): array
    {
        $errors = [];

        if (empty($data["full_name"]) && (empty($data["email"]) && $is_new)) {
            $errors[] = "Name is required.";
            $errors[] = "Email is required.";
        }

        if (array_key_exists("email", $data) && (empty($data["email"]) && $is_new)) {
            if (filter_var($data["email"], FILTER_VALIDATE_EMAIL) == false) {
                $errors[] = "Email inserted is invalid.";
            }
        }

        return $errors;
    }
}
