"use client";

import { use, useState } from "react";
import HotelTable from "@/components/HotelTable";
import { hotels } from "@/data/hotels";

type TripDetailPageProps = {
  params: Promise<{
    id: string;
  }>;
};

export default function TripDetailPage({ params }: TripDetailPageProps) {
  const { id } = use(params);

  const [tab, setTab] = useState("宿");
  const [sort, setSort] = useState("price");
  const [stationFilter, setStationFilter] = useState("all");

  // フィルター
  const filteredHotels =
    stationFilter === "all"
      ? hotels
      : hotels.filter((hotel) => hotel.station === stationFilter);

  // 並び替え
  const sortedHotels = [...filteredHotels].sort((a, b) => {
    if (sort === "price") return a.price - b.price;
    if (sort === "walk") return a.walk - b.walk;
    return 0;
  });

  return (
    <main className="p-10">
      <h1 className="text-2xl font-bold">旅行詳細</h1>
      <p className="mt-4">旅行ID：{id}</p>

      {/* 並び替え */}
      <div className="mt-4">
        <select
          className="border p-2"
          value={sort}
          onChange={(e) => setSort(e.target.value)}
        >
          <option value="price">安い順</option>
          <option value="walk">近い順</option>
        </select>
      </div>

      {/* フィルター */}
      <div className="mt-4">
        <select
          className="border p-2"
          value={stationFilter}
          onChange={(e) => setStationFilter(e.target.value)}
        >
          <option value="all">すべて</option>
          <option value="渋谷">渋谷</option>
          <option value="新宿">新宿</option>
        </select>
      </div>

      {/* タブ */}
      <div className="mt-6 flex gap-2">
        <button className="border px-4 py-2" onClick={() => setTab("宿")}>
          宿
        </button>
        <button className="border px-4 py-2" onClick={() => setTab("スポット")}>
          スポット
        </button>
        <button className="border px-4 py-2" onClick={() => setTab("投票")}>
          投票
        </button>
        <button className="border px-4 py-2" onClick={() => setTab("予算")}>
          予算
        </button>
        <button
          className="border px-4 py-2"
          onClick={() => setTab("スケジュール")}
        >
          スケジュール
        </button>
      </div>

      {/* 表示 */}
      <div className="mt-6">
        {tab === "宿" && <HotelTable data={sortedHotels} />}
        {tab === "スポット" && <p>スポット一覧がここに出る</p>}
        {tab === "投票" && <p>投票画面</p>}
        {tab === "予算" && <p>予算画面</p>}
        {tab === "スケジュール" && <p>スケジュール</p>}
      </div>
    </main>
  );
}
