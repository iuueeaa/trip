"use client";

import { useState } from "react";
import TripCard from "@/components/TripCard";
import TripForm from "@/components/TripForm";

export default function Home() {
  const [trips, setTrips] = useState([
    { id: 1, title: "京都旅行" },
    { id: 2, title: "沖縄旅行" },
  ]);

  const addTrip = (title: string) => {
    setTrips([...trips, { id: Date.now(), title }]);
  };

  return (
    <main className="p-10">
      <h1 className="text-2xl font-bold">旅行一覧</h1>

      <TripForm onAdd={addTrip} />

      {trips.map((trip) => (
        <TripCard key={trip.id} id={trip.id} title={trip.title} />
      ))}
    </main>
  );
}
