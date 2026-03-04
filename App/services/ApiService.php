<?php

class ApiService extends Service
{
    public function genreList() {
        return Genre::find()->get();
    }

    public function singleGenre($genre_id) {
        return Genre::find(['genre_id' => $genre_id])->first();
    }

    public function create_genre($data) {
        return Genre::create([
            'genre_name' => $data['genre_name'],
            'created_on' => date('Y-m-d H:i:s'),
            'popularity' => $data['popularity'],
            'image' => $data['image']
        ]);
    }

    public function delete_genre($genre_id) {
        $genre = Genre::find(['genre_id' => $genre_id])->first();
        if ($genre) {
            // Using inherited $this->db from Service
            $this->db->delete('genre', ['genre_id' => $genre_id]);
        }
    }

    public function edit_genre($data) {
        $postData = ['genre_name' => $data['genre_name']];
        $this->db->update('genre', $postData, ['genre_id' => $data['genre_id']]);
    }

    /*
    ** ADMIN FUCTIONS
    */
    public function get_all_users() {
        // Advanced joins can utilize raw connections via inherited $this->db
        return $this->db->select('SELECT u.user_id, u.user_email, u.user_fname, u.user_lname, r.role_name FROM users AS u JOIN role AS r ON u.role = r.role_value');
    }

    public function search_users() {
         return User::find()->get();
    }
}
?>
