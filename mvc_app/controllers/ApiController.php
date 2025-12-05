<?php
namespace Controllers;

use Core\Controller;
use Models\Model;

class ApiController extends Controller {
    private $model;
    
    public function __construct($request, $response) {
        parent::__construct($request, $response);
        $this->model = new Model();
        
        // Устанавливаем заголовки для API
        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $this->response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $this->response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }
    
    // GET /api/data
    public function getAllData() {
        if (!$this->request->isMethod('GET')) {
            $this->response->json(['error' => 'Method not allowed'], 405);
        }
        
        $data = $this->model->getAll();
        $this->response->json([
            'success' => true,
            'data' => $data,
            'count' => count($data)
        ]);
    }
    
    // GET /api/data/{id}
    public function getData() {
        if (!$this->request->isMethod('GET')) {
            $this->response->json(['error' => 'Method not allowed'], 405);
        }
        
        $id = $this->request->get('id');
        $data = $this->model->getById($id);
        
        if ($data) {
            $this->response->json([
                'success' => true,
                'data' => $data
            ]);
        } else {
            $this->response->json([
                'success' => false,
                'error' => 'Data not found'
            ], 404);
        }
    }
    
    // POST /api/data
    public function createData() {
        if (!$this->request->isMethod('POST')) {
            $this->response->json(['error' => 'Method not allowed'], 405);
        }
        
        $input = $this->request->post();
        
        if (empty($input['data_string'])) {
            $this->response->json(['error' => 'Data string is required'], 400);
        }
        
        $result = $this->model->create($input);
        
        if ($result) {
            $this->response->json([
                'success' => true,
                'message' => 'Data created successfully',
                'id' => $result
            ], 201);
        } else {
            $this->response->json(['error' => 'Failed to create data'], 500);
        }
    }
    
    // PUT /api/data/{id}
    public function updateData() {
        if (!$this->request->isMethod('PUT')) {
            $this->response->json(['error' => 'Method not allowed'], 405);
        }
        
        $id = $this->request->get('id');
        $input = $this->request->post();
        
        if (empty($input['data_string'])) {
            $this->response->json(['error' => 'Data string is required'], 400);
        }
        
        $result = $this->model->update($id, $input);
        
        if ($result) {
            $this->response->json([
                'success' => true,
                'message' => 'Data updated successfully'
            ]);
        } else {
            $this->response->json(['error' => 'Failed to update data'], 500);
        }
    }
    
    // DELETE /api/data/{id}
    public function deleteData() {
        if (!$this->request->isMethod('DELETE')) {
            $this->response->json(['error' => 'Method not allowed'], 405);
        }
        
        $id = $this->request->get('id');
        $result = $this->model->delete($id);
        
        if ($result) {
            $this->response->json([
                'success' => true,
                'message' => 'Data deleted successfully'
            ]);
        } else {
            $this->response->json(['error' => 'Failed to delete data'], 500);
        }
    }
}
?>