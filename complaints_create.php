<?xml version="1.0" encoding="utf-8"?>
<ScrollView xmlns:android="http://schemas.android.com/apk/res/android"
    android:layout_width="match_parent"
    android:layout_height="match_parent"
    android:padding="16dp">

    <LinearLayout
        android:layout_width="match_parent"
        android:layout_height="wrap_content"
        android:orientation="vertical">

        <TextView
            android:layout_width="wrap_content"
            android:layout_height="wrap_content"
            android:text="Create New Complaint"
            android:textSize="20sp"
            android:textStyle="bold"
            android:layout_gravity="center"
            android:paddingBottom="16dp" />

        <EditText
            android:id="@+id/etTitle"
            android:layout_width="match_parent"
            android:layout_height="wrap_content"
            android:hint="Title" />

        <EditText
            android:id="@+id/etDescription"
            android:layout_width="match_parent"
            android:layout_height="wrap_content"
            android:hint="Description"
            android:minLines="3"
            android:gravity="top"
            android:layout_marginTop="12dp" />

        <EditText
            android:id="@+id/etLocation"
            android:layout_width="match_parent"
            android:layout_height="wrap_content"
            android:hint="Location"
            android:layout_marginTop="12dp" />

        <Button
            android:id="@+id/btnSubmitComplaint"
            android:layout_width="match_parent"
            android:layout_height="wrap_content"
            android:text="Submit Complaint"
            android:layout_marginTop="20dp" />

    </LinearLayout>
</ScrollView>
