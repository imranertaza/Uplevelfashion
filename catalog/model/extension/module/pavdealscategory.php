<?php

class ModelExtensionModulePavdealscategory extends Model
{
    public function getProductSpecials($data = array(), $category_id) {
        $this->load->model( 'catalog/product' );
        $filter = "  ";
        if(isset($data['start_date']) && isset($data['to_date'])){
            $filter .= " AND ((ps.date_start <= '{$data['to_date']}') AND (ps.date_end >= '{$data['start_date']}')) ";
        }

        $join = "";
        
        $sql = "SELECT DISTINCT ps.product_id,ps.date_start,ps.date_end,ps.price AS special, (SELECT AVG(rating) FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = ps.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) ".$join." LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p2c.category_id = '" . (int)$category_id . "' AND p.status = '1' AND p.date_available <= NOW() AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND
            (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' {$filter} GROUP BY ps.product_id";

        $sort_data = array(
            'pd.name',
            'p.date_added',
            'p.model',
            'ps.price',
            'rating',
            'p.sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
                $sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
            } else {
                $sql .= " ORDER BY " . $data['sort'];
            }
        } else {
            $sql .= " ORDER BY p.sort_order";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC, LCASE(pd.name) DESC";
        } else {
            $sql .= " ASC, LCASE(pd.name) ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $product_data = array();

        $query = $this->db->query($sql);
        
        foreach ($query->rows as $result) {
            $product_data[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
            $product_data[$result['product_id']]["date_start"] = $result["date_start"];
            $product_data[$result['product_id']]["date_end"] = $result["date_end"];
            $product_data[$result['product_id']]["special"] = $result["special"];
        }

        return $product_data;
    }

    public function getAllProductSpecials($data = array()) {
        $this->load->model( 'catalog/product' );
        $filter = "  ";
        if(isset($data['start_date']) && isset($data['to_date'])){
            $filter .= " AND ((ps.date_start <= '{$data['to_date']}') AND (ps.date_end >= '{$data['start_date']}')) ";
        }

        $join = "";
        
        $sql = "SELECT DISTINCT ps.product_id,ps.date_start,ps.date_end,ps.price AS special, (SELECT AVG(rating) FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = ps.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) ".$join." LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND
            (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' {$filter} GROUP BY ps.product_id";

        $sort_data = array(
            'pd.name',
            'p.date_added',
            'p.model',
            'ps.price',
            'rating',
            'p.sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
                $sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
            } else {
                $sql .= " ORDER BY " . $data['sort'];
            }
        } else {
            $sql .= " ORDER BY p.sort_order";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC, LCASE(pd.name) DESC";
        } else {
            $sql .= " ASC, LCASE(pd.name) ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $product_data = array();

        $query = $this->db->query($sql);
        
        foreach ($query->rows as $result) {
            $product_data[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
            $product_data[$result['product_id']]["date_start"] = $result["date_start"];
            $product_data[$result['product_id']]["date_end"] = $result["date_end"];
            $product_data[$result['product_id']]["special"] = $result["special"];
        }

        return $product_data;
    }

}