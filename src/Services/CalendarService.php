<?php

namespace Blome\Services;

class CalendarService
{
    private SupabaseClientInterface $client;

    public function __construct(SupabaseClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Verifica se existe conflito de horário para a Turma ou Professor.
     * ADICIONADO: Parâmetro $token para ter permissão de ver a agenda.
     */
    public function checkConflict(string $day, string $start, string $end, string $classId, string $profId, string $token, ?string $ignoreId = null): ?string
    {
        // 1. Verifica conflito da TURMA
        $queryClass = "professors_schedule?class_id=eq.$classId&day_of_week=eq.$day";
        $queryClass .= "&start_time=lt.$end&end_time=gt.$start";
        
        if ($ignoreId) $queryClass .= "&id=neq.$ignoreId";

        // Usa o token do Admin para ter certeza que vê tudo
        $conflictClass = $this->client->fetch($queryClass, $token);

        if (!empty($conflictClass) && !isset($conflictClass['error'])) {
            return "Esta turma já tem aula neste horário (" . substr($conflictClass[0]['start_time'], 0, 5) . " - " . substr($conflictClass[0]['end_time'], 0, 5) . ").";
        }

        // 2. Verifica conflito do PROFESSOR
        $queryProf = "professors_schedule?professor_id=eq.$profId&day_of_week=eq.$day";
        $queryProf .= "&start_time=lt.$end&end_time=gt.$start";
        
        if ($ignoreId) $queryProf .= "&id=neq.$ignoreId";

        $conflictProf = $this->client->fetch($queryProf, $token);

        if (!empty($conflictProf) && !isset($conflictProf['error'])) {
            return "O professor selecionado já está ocupado neste horário em outra turma.";
        }

        return null; // Sem conflitos
    }

    /**
     * Busca o ID da relação Professor-Matéria.
     * ADICIONADO: Parâmetro $token.
     */
    public function getProfessorSubjectId(string $profId, string $subjectId, string $token): ?string
    {
        $query = "professors_subjects?select=id&professor_id=eq.$profId&subject_id=eq.$subjectId";
        
        // Agora passamos o token correto, então o banco vai responder!
        $result = $this->client->fetch($query, $token);

        if (!empty($result) && !isset($result['error']) && isset($result[0]['id'])) {
            return $result[0]['id'];
        }
        return null;
    }

    public function createLesson(array $data): array
    {
        // Para CRIAR, usamos a Service Key (Admin Supremo) para garantir que nada bloqueie
        global $supabaseUrl, $supabaseServiceKey;
        $res = \supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "professors_schedule", "POST", $data, null);
        
        if (isset($res['error'])) return ['error' => $res['error']['message']];
        return ['success' => true];
    }

    public function updateLesson(string $id, array $data): array
    {
        global $supabaseUrl, $supabaseServiceKey;
        $res = \supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "professors_schedule?id=eq.$id", "PATCH", $data, null);
        
        if (isset($res['error'])) return ['error' => $res['error']['message']];
        return ['success' => true];
    }

    public function deleteLesson(string $id): bool
    {
        global $supabaseUrl, $supabaseServiceKey;
        $res = \supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "professors_schedule?id=eq.$id", "DELETE", null, null);
        return !isset($res['error']);
    }
}
?>